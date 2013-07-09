<?php

namespace Zizoo\BaseBundle\Twig;

class BaseExtension extends \Twig_Extension
{
    
    public function getFilters()
    {
        return array(
            'displayAmount'         => new \Twig_Filter_Method($this, 'displayAmount'),
            'parentBlockPrefix'     => new \Twig_Filter_Method($this, 'parentBlockPrefix'),
            'rootLabel'             => new \Twig_Filter_Method($this, 'rootLabel'),
        );
    }

    public function displayAmount($dummy=null, $amount)
                {
        return number_format($amount, 2);
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
   
    public function getName()
    {
        return 'base_extension';
    }
}
?>
