<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

trait FileTrait
{

    public function getIframeUrl($route, $params = [])  
    {
        $routeParams = ['r' => $route];
        $urlParams = [];
        $urlParams = array_merge($urlParams, $params);
        $params = array_merge($routeParams, ['params' => $urlParams]);
        return $this->generateUrl('app_dashboard_iframe', $params);
    }


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
    private function renderPdf($template, $vars, $options=[], $showResponse = true)
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


        $mpdf->watermark("UFR SEG", 45, 90, 0.1);

                

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