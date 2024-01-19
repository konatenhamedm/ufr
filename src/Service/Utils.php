<?php

namespace App\Service;

use App\Attribute\Module;
use App\Attribute\RoleMethod;
use App\Attribute\Source;
use App\Controller\FileTrait;
use App\Entity\Colonne;
use App\Entity\Fichier;
use App\Repository\ValidationRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpWord\PhpWord;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Type;
use Twig\Environment;

class Utils
{
    public function __construct(
       private FileUploader $fileUploader
    )
    {

    }

    use FileTrait;

    const MOIS = [
        1 => 'Janvier',
        'Février',
        'mars',
        'avril',
        'mai',
        'juin',
        'juillet',
        'août',
        'septembre',
        'octobre', 
        'novembre',
        'décembre'
    ];

    const BASE_PATH = 'formation/certificat';


    public static function formatNumber($value, $decimal = 0, $sep = '.', $thousandSep = ' ')
    {
        $value = $value ? strval($value) : '0';
        $decimalLength = $decimal;
        if (strpos($value, '.')) {
            [,$decimal] = explode('.', $value);
            if (substr_count($decimal, '0') != strlen($decimal)) {
                $decimalLength = strlen($decimal);
            }
        }

        return preg_replace('/\.00$/', '', number_format($value, $decimalLength, $sep, $thousandSep));
    }


    public static function getIdValue($value)
    {
        if (is_object($value)) {
            return $value->getId();
        }
        return $value;
    }


    public static function getFromArray(array $array, string $key)
    {
        return array_map(function ($row) use ($key) {
            return $row[$key];
        },$array);
    }


    public static function getValue($data, ?string $prop = null)
    {
        if ($data instanceof DateTime) {
            return $data->format('d/m/Y');
        }

        return $data && $prop ? $data->{"get".ucfirst(strtolower($prop))}() : null;
    }



    public static function getInitialFromNames($nom, $prenom)
    {
        $prenom = trim(str_replace(['epoux', 'épouse', 'epouse', 'épse', 'epse', 'epx'], '', $prenom));
        $nom = trim(str_replace(['epoux', 'épouse', 'epouse'], '', $nom));
        preg_match_all('/\b\w/u', $prenom.' '.$nom, $matches);
        return mb_strtoupper(implode('', $matches[0]));
    }


    public static function reverseFormat($string)
    {
        $value = floatval(strtr(trim($string), [' ' => '', ',' => '.']));
        return preg_replace('/[\.,]00$/', '', $value);
    }


    public static function  localizeDate($value, $time = false)
    {
        $fmt = new \IntlDateFormatter(
            'fr',
            \IntlDateFormatter::FULL,
            $time ? \IntlDateFormatter::FULL : \IntlDateFormatter::NONE
        );
        return $fmt->format($value instanceof \DateTimeInterface ? $value : new \DateTime($value));
    }


    public static function getAllSources(EntityManagerInterface $em): array
    {
        $entities = $em->getConfiguration()->getMetadataDriverImpl()->getAllClassNames();
        $sources = [];
        foreach ($entities as $entity) {
            $refClass = new ReflectionClass($entity);
           
            if ($refClass->getAttributes(Source::class)) {
                $sources[class_basename($entity)] = $entity;
            }
        }

        return $sources;
    }


    public static function getMethods($controllerName, $module, $controller): array
    {
        $methods = [];
        $hasChildren = [];
        try {
            $refClass = new ReflectionClass($controllerName);
          
    
            if ($moduleAttributes = $refClass->getAttributes(Module::class)) {
                //$excludes = $moduleAttributes['excludes'];
                $excludes = ['show'];
                $children = [];
                foreach ($moduleAttributes as $moduleAttribute) {
                    $argModule = $moduleAttribute->getArguments();
                    $excludes = array_merge($excludes, $argModule['excludes'] ?? []);
                    $children = array_merge($children, $argModule['children'] ?? []);
                }

               

                foreach ($refClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                   
                    if (!in_array($method->getName(), $excludes) && $method->getAttributes(Route::class)) {
                        $roleInfos = $method->getAttributes(RoleMethod::class);
                        $controller = strtoupper($controller);
                        $props = [];
                        $methodName = $method->getName();
                        foreach ($roleInfos as $roleInfo) {
                            $props = $roleInfo->getArguments();
                        }
                        $suffix = strtoupper($props['as'] ?? $methodName);
                        $options = $props['options'] ?? [];

                        $mapRole = "ROLE_{$suffix}";

                        $title = '';
                        if (isset($options['titles'][$methodName])) {
                            $title = $options['titles'][$methodName];
                        } else {
                            if (isset($props['title']) && !isset($children[$methodName])) {
                                $title = $props['title'];
                            } elseif (isset(Role::getRoles()[$mapRole])) {
                                $title = Role::getRoles()[$mapRole];
                            } else {
                                $title = $method->getName();
                            }
                        }
                        

                        $key = "ROLE_{$module}_{$controller}_{$suffix}";
                        $methods[$key] = $title; 
                        foreach ($children as $_method => $mapChilds) {
                            foreach ($mapChilds as $child) {
                                if ($_method == $methodName) {
                                    $key = "ROLE_{$module}_{$controller}_{$suffix}_".strtoupper($child);
                                    $mapRole = strtoupper("ROLE_{$child}");
                                    $title = Role::getRoles()[$mapRole] ?? $child;
                                    if (isset($options['titles'][$child])) {
                                        $title = $options['titles'][$child];
                                    }
                                    $methods[$key] = $title;
                                }
                            }
                        }   
                    }
                }
            }
           
        } catch (ReflectionException $e) {
        
        }
        
        return $methods;
       
    }


    public static function convertValue($value, $typeDonnee, $source = null, EntityManagerInterface $em = null)
    {
        if ($typeDonnee == 'EntityType') {
            return $em ? $em->getRepository($source)->find($value) : '';
        } elseif ($typeDonnee == 'DateType') {
            return new \DateTime($value);
        } elseif ($typeDonnee == 'NumberType') {
            return intval($value);
        } else {
            return $value;
        }
    }


    public static function getFieldByColonne(Colonne $colonne)
    {
        $type = $colonne->getTypeDonnee();
        $isNumber = false;
        $source = $colonne->getSource();
        $valeurs = explode(',', $colonne->getListeValeur());
        $id = $colonne->getId();
        $colonneAttrs = [];

            
        $props = [
            'required' => $colonne->getRequired(),
            'label' => $colonne->getLibelle()
        ];

        $valeurs = $valeurs ? array_combine($valeurs, $valeurs): [];

        $namespace = 'Symfony\\Component\\Form\\Extension\\Core\\Type\\';
         
        $fullType = $namespace.$type;
        if ($type == 'EntityType') {
            $fullType = "Symfony\\Bridge\\Doctrine\\Form\\Type\\{$type}";
            $props['class'] = $source;
            $props['required'] = false;
            $props['placeholder'] = '---';
            $props['attr'] = ['class' => 'has-select2'];
            $props['choice_label'] = function ($e) {
                return $e->{'get'.ucfirst($e::DEFAULT_CHOICE_LABEL)}();
            };
        } elseif ($type == 'CheckboxType') {
            $type    = 'ChoiceType';
            $isArray = true;
            $props   = array_merge($props, [
                'expanded' => true,
                'multiple' => true,
                'choices'  => $valeurs,
            ]); 
        } elseif ($type == 'ChoiceType') {
            $props   = array_merge($props, [
                'expanded' => true,
                'multiple' => false,
                'choices'  => $valeurs,
                'attr' => ['class' => 'has-select2']
            ]);
        } elseif ($type == 'RadioType') {
            $props   = array_merge($props, [
                'expanded' => true,
                'multiple' => false,
                'choices'  => $valeurs,
            ]);
        } elseif ($type == 'DateType') {
            $props = array_merge($props, [
                'attr' => ['class' => 'datepicker no-auto skip-init'],
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
            ]);
        }

        $idColonne = $colonne->getId();
        $numbers = [];

        if (in_array($type, ['NumberType'])) {
            $isNumber = true;
            if (isset($props['attr']['class'])) {
                //$props['attr']['class'] .= ' input-money text-end';
                $props['attr']['class'] .= 'text-end input-money';
            } else {
                $props['attr']['class'] = 'text-end input-money';
                //$props['attr']['class'] = ' input-money text-end';
            }

            //$props['constraints'] = [new Type('digit')];
            $numbers[] = $idColonne;
        }

        if (isset($props['attr']['class'])) {
            $props['attr']['class'] .= ' field-'.$idColonne.' '.$colonne->getCode();
        } else {
            $props['attr']['class'] = ' field-'.$idColonne.' '.$colonne->getCode();
        }

        $props['attr']['data-field'] = $idColonne;
        $props['attr']['data-code'] = $colonne->getCode();
        $props['attr']['data-calcul'] = $colonne->getCalcul();
        if ($props['attr']['data-calcul']) {
            $props['attr']['class'] = ($props['attr']['class'] ?? ''). ' has-calcul';
        }

        $props['attr']['class'] = ($props['attr']['class'] ?? ''). ' col-field';
        $props['attr']['data-ref'] = $colonne->getReference();
        $props['attr']['data-result'] = $colonne->getResultat();
        $props['attr']['data-formule'] = Calcul::TOKENS[$colonne->getCalcul()] ?? '';

        if ($colonne->getCode() == 'pourcentage' || $colonne->getCalcul() == 2) {
            $props['constraints'] = [new Range(['min' => 0, 'max' => 100])];
            $props['attr']['max'] = 100;
        }

        return new FieldInfo($id, $fullType, $props, $isNumber);
    }


    public static function toLabel($valeur, $typeDonnee, $source, $em)
    {
        if ($typeDonnee != 'EntityType') {
            return $valeur;
        }

        $data = static::convertValue($valeur, $typeDonnee, $source, $em);
        if (is_object($data) && $data->getId()) {
           
            $labelProperty = $data::DEFAULT_CHOICE_LABEL;
            $method = 'get'.ucfirst($labelProperty);
            if (method_exists($data, $method)) {
               
                return $data->{$method}();
            }
           
        }
    }

    /**
     * @author Jean Mermoz Effi <mangoua.effi@uvci.edu.ci>
     * Cette function pemet la generation d'un nombre numerique
     * Avec une génération par defaut de 8 caractères
     * @param $len
     * @return mixed
     */
    public function generateNum($len = 8, $type = 'alphabet')
    {
        $alphabet = '0123456789';
        $alphanum = $alphabet . implode('', range('a', 'z'));

        $data = $type == 'alphabet' ? $alphabet : $alphanum;

        if ($len < 1) {
            throw new \InvalidArgumentException('La taille du generateur doit être positif !');
        }

        $str      = '';
        $alphamax = strlen($data) - 1;
        if ($alphamax < 1) {
            throw new \InvalidArgumentException('Invalid alphabet');
        }

        for ($i = 0; $i < $len; ++$i) {
            $str .= $data[random_int(0, $alphamax)];
        }

        return $str;
    }

    /**
     * @author Jean Mermoz Effi <mangoua.effi@uvci.edu.ci>
     * Cette fonction permet la création d'un nouveau fichier pour une entité liée
     *
     * @param mixed $filePath
     * @param mixed $entite
     * @param mixed $filePrefix
     * @param mixed $uploadedFile
     *
     * @return Fichier|null
     */
    public function sauvegardeFichier($filePath, $filePrefix, $uploadedFile, string $basePath = self::BASE_PATH): ?Fichier
    {
        if (!$filePrefix) {
            return false;
        }

        $path = $filePath;
        $this->fileUploader->upload($uploadedFile, null, $path, $filePrefix, true);

        $fileExtension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        $fichier = new Fichier();
        $fichier->setAlt(basename($path));
        $fichier->setPath($basePath);
        $fichier->setSize(filesize($path));
        $fichier->setUrl($fileExtension);
        
        return $fichier;
    }


     /**
     * @return mixed
     */
    public static function getUploadDir($path, $uploadDir, $create = false)
    {
        $path = $uploadDir . '/' . $path;
       
        if ($create && !is_dir($path)) {
            mkdir($path, 0777, true);
        }
        return $path;
    }


    public static function wordHeader(PhpWord $phpWord, string $uploadDir, &$section, $mode = 'portrait', $fontName = 'Arial Narrow', $type = 'auto')
    {
        $header = $section->addHeader();
        $header->setType($type);
        $fontName       = $fontName ?: 'Arial Narrow';
        $logoCellWidths = ['portrait' => 1000, 'landscape' => 2000];
        $textCellWidths = ['portrait' => 6000, 'landscape' => 20000];
        $lastCellWidths = ['portrait' => 1000, 'landscape' => 2000];
        $logoWidths     = ['portrait' => 1100, 'landscape' => 800];
        $headerFonts    = [
            'portrait' => ['size' => 6, 'italic' => true, 'name' => $fontName]
            , 'landscape' => ['size' => 8, 'italic' => true, 'name' => $fontName],
        ];

        $textParagraphStyle = ['align' => Alignment::VERTICAL_CENTER, 'spaceAfter' => 0];

        $headerFont = $headerFonts[$mode];

        $w1 = $logoCellWidths[$mode];
        $w2 = $textCellWidths[$mode];
        $w3 = $lastCellWidths[$mode];

        $imageWidth = $logoWidths[$mode];
       

        $styleTable = ['borderSize' => 0, 'borderColor' => 'ffffff', 'cellPadding' => 100, 'cellMargin' => 100];
        $phpWord->addTableStyle('headerTable', $styleTable);
        $table = $header->addTable('headerTable');
        $table->addRow();
        $cell = $table->addCell($w1);
        $cell->addImage($uploadDir . '/media/pdf/entete.jpg', ['width' => $imageWidth, 'align' => 'center']);
        //$headerFont = ['size' => 8, 'italic' => true, 'name' => 'Arial Narrow'];
        $cell = $table->addCell($w2);
       

        return $phpWord;

    }


    public static function wordFooter(&$section, $mode = 'portrait')
    {
        $footer = $section->addFooter();

        $name = 'Arial';
        
        $footerStyle = ['size' => 8, 'color' => '333333', 'italic' => true, 'name' => $name, 'bold' => true];
        $center      = ['align' => Alignment::VERTICAL_CENTER, 'spaceAfter' => 0];

        $textbox = $footer->addTextBox(
            [
                //'align'       => 'center',
                'width'       => $mode == 'portrait' ? 500 : 700,
                'height'      => 25,
                'borderSize'  => 0,

                'borderColor' => '#FFFFFF',

                //'positioning' => \PhpOffice\PhpWord\Style\TextBox::POSITION_RELATIVE_TO_LMARGIN,
                //'marginLeft' => 5000,

                'innerMargin' => 10,
            ]
        );

        $current = [
            'KPL - Zone industrielle Vridi Côte d’ivoire – Abidjan –', 
            'Tél. : (+225) 27 21 2 72604 – Adresse Mail : info@kuyopipeline.com'
        ];

        

        $paragraphStyle = array_merge($center, ['borderTopSize' => 20, 'borderTopColor' => '#cf2e2e']);
        $textbox->addText($current[0], $footerStyle, $paragraphStyle);
        $textbox->addText($current[1], $footerStyle, $center);
    }



     /**
     * @param $template
     * @param $vars
     */
    public static function renderPdf($template, $vars, Environment $environment, ?UserInterface $user = null, $options=[])
    {

        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $orientation = $options['orientation'] ?? 'P';
        $formatSuffix = $orientation == 'P' ? '' : '-L';
        $destination = $options['destination'] ?? 'I';
        $fileName = $options['file_name'] ?? null;

        $mpdf = new \Mpdf\Mpdf([
            'orientation' => $orientation, 
            'format' => ($options['format'] ?? 'A4').$formatSuffix,
            'mode' => 'utf-8',
            'fontDir' => array_merge($fontDirs, $options['fontDir'] ?? []),
            'fontdata' => $fontData + [
                'arial' => [
                    'I' => 'ariali.ttf',
                    'B' => 'arialb.ttf',
                    'BI' => 'arialbi.ttf',
                    'R' => 'arial.ttf',
                    'L' => 'ariall.ttf',
                ],
                'trebuchet' => [
                    'I' => 'Trebucheti.ttf',
                    'R' => 'trebuc.ttf',
                    'B' => 'TREBUCBD.ttf',
                ]
            ],
        ]);

        $mpdf->shrink_tables_to_fit = 1;

        $mpdf->WriteHTML($environment->render($template, $vars));
        $mpdf->author = $user ? $user->getNomComplet() : 'KPL';
        

        if (isset($options['addPage'])) {
            $mpdf->AddPage();
        }

        
        $data = $mpdf->Output($fileName, $destination);

        return $data;
    }


    public static function getValidateurs(object $obj, ValidationRepository $validationRepository)
    {
       
        $allEtats = $obj::VALIDATIONS;
        $validateurs = [];

        foreach ($allEtats as $label => $etat) {
            $validation = $validationRepository->getLastValidation($etat, $obj, get_class($obj));
            if ($validation) {
                $validateurs[$etat] = [
                    'label' => $label,
                    'nom' => $validation?->getUtilisateur()?->getNomComplet(),
                    'date' => $validation?->getDateCreation()
                ];
            }
        }
        return $validateurs;
    }
}