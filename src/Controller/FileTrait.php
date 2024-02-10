<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

trait FileTrait
{

    /**
     * @return mixed
     */
    public function getUploadDir($path, $create = false)
    {
        $path = $this->getParameter('upload_dir') . '/' . $path;
        if ($create && !is_dir($path)) {
            mkdir($path, 0777, true);
        }
        return $path;
    }



    /**
     * @param $template
     * @param $vars
     */
    private function renderPdf($template, $vars, $options = [], $showResponse = true)
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
            'format' => ($options['format'] ?? 'A4') . $formatSuffix,
            'mode' => 'utf-8',
            'fontDir' => array_merge($fontDirs, $options['fontDir'] ?? []),
            'fontdata' => $fontData + [
                'comfortaa' => [
                    'B' => 'Comfortaa-Bold.ttf',
                    'R' => 'Comfortaa-Regular.ttf',
                    'L' => 'Comfortaa-Light.ttf',
                ],
                'fontawesome' => [
                    'R' => 'fontawesome-webfont.ttf',
                ],
                'arial' => [
                    'I' => 'ariali.ttf',
                    'B' => 'arialb.ttf',
                    'BI' => 'arialbi.ttf',
                    // 'R' => 'arial.ttf',
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

        $mpdf->WriteHTML($this->renderView($template, $vars));
        $mpdf->author = $this->getUser()->getNomComplet();
        $mpdf->showImageErrors = true;

        if (isset($options['protected']) && $options['protected']) {
            $mpdf->SetProtection(['print']);
        }

        $mpdf->showWatermarkText = $options['showWaterkText'] ?? false;
        $mpdf->showWatermarkImage = $options['showWaterkImage'] ?? false;


        $mpdf->watermark($options['entreprise'], 45, 90, 0.1);
        if ($options['watermarkImg'] != "") {
            $mpdf->watermarkImg($options['watermarkImg'], 0.1);
        }
        //  $mpdf->watermarkImg($imgFiligrame, 0.1);
        // $mpdf->Image("", 10, 10, 30, 0, 'PNG', '', '', true, 150, '', false, false, 0, true, false, false);





        /*$mpdf->SetAlpha(0.5); // default is 0.2
        $mpdf->Rotate(45);
        $mpdf->Text(50, 10, 'PAID!'); // specify position here
        // reset
        $mpdf->Rotate(0);
        $mpdf->SetAlpha(1);*/


        if (isset($options['addPage'])) {
            $mpdf->AddPage();
        }


        $data = $mpdf->Output($fileName, $destination);

        if ($showResponse) {
            return new Response();
        }
        return $data;
    }
}
