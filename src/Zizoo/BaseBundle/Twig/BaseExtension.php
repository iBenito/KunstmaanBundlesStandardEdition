<?php

namespace Zizoo\BaseBundle\Twig;

class BaseExtension extends \Twig_Extension
{
    
    public function getFilters()
    {
        return array(
            'displayAmount'         => new \Twig_Filter_Method($this, 'displayAmount'),
            'extractJavaScript'     => new \Twig_Filter_Method($this, 'extractJavaScript'),
            'parentBlockPrefix'     => new \Twig_Filter_Method($this, 'parentBlockPrefix'),
            'rootLabel'             => new \Twig_Filter_Method($this, 'rootLabel'),
            'absoluteAsset'         => new \Twig_Filter_Method($this, 'absoluteAsset'),
            'completeness'          => new \Twig_Filter_Method($this, 'completeness'),
        );
    }

    public function displayAmount($dummy=null, $amount)
                {
        return number_format($amount, 2);
    }

    public function extractJavaScript($html)
    {
        $htmlDom    = new \DOMDocument();
        $jsDom      = new \DOMDocument();
        $jsRoot     = new \DOMElement('div');
        $jsDom->appendChild($jsRoot);

        if ($htmlDom->loadXML($html)){
            $scripts = $htmlDom->getElementsByTagName('script');
            for ($i=0; $i<$scripts->length; $i++){
                $script = $scripts->item($i);
                $newScript = $jsDom->importNode($script, true);
                $jsRoot->appendChild($newScript);
                //$script->parentNode->removeChild($script);
            }
        }

        $html   = $htmlDom->saveXML($htmlDom->documentElement);
        $js     = htmlspecialchars_decode($jsDom->saveXML($jsDom->documentElement), ENT_QUOTES | ENT_SUBSTITUTE);
        return array($html, $js);
    }
      
    public function parentBlockPrefix($blockPrefixes)
    {
        $numPrefixes = count($blockPrefixes);
        if ($numPrefixes>1){
            return $blockPrefixes[$numPrefixes-2];
        } else {
            return '';
        }
    }
    
    public function rootLabel($form, $class)
    {
        $labelFound = false;
        while ( $form->parent != null){
            $form = $form->parent;
            if (array_key_exists('label', $form->vars) && $form->vars['label']){
                $labelFound = true;
                break;
            }
        }
        if ($labelFound){
            return $class;
        } else {
            return $class . ' root';
        }
    }
    public function absoluteAsset($url, $request)
    {
        return $request->getScheme() . '://' . $request->getHost() . $url;
    }

    private function completenessLevel($completeness, $level)
    {
        $class = $completeness >= $level ? ' class="complete"':'';
        return '<li'.$class.'><span>'.$level.'</span></li>'.PHP_EOL;
    }

    public function completeness($completeness, $max = 3)
    {
        $output = array();

        $output[] = '<h4>Profile Completeness'.PHP_EOL;
        if ($completeness <= $max){
            $output[] = '<span class="icon cross"></span>'.PHP_EOL;
        }
        $output[] = '<a href="#" class="help"><span class="tooltip">Complete Profile Requires:</br></br>- Profile Details</br>- Profile Picture</br>- Verification</span></a>'.PHP_EOL;
        $output[] = '</h4>'.PHP_EOL;
        $output[] = '<ul class="clearfix">'.PHP_EOL;

        for ($level=1; $level<=$max; $level++){
            $output[] = $this->completenessLevel($completeness, $level);
        }
        $output[] = '</ul>'.PHP_EOL;

        echo implode('', $output);
    }
   
    public function getName()
    {
        return 'base_extension';
    }
}
?>
