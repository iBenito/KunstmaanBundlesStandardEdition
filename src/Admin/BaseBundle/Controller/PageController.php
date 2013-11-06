<?php
namespace Admin\BaseBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\DBALException;

class PageController extends Controller {
    
    /**
     * Displays the login widget.
     * 
     * @param boolean $showLoginForm    True to show login form directly in widget if user not logged in.
     * @author Alex Fuckert <alexf83@gmail.com>
     */
    public function loginWidgetAction($showLoginForm=false)
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        $isLoggedIn = false;
        $securityContext = $this->container->get('security.context');
        if( $securityContext->isGranted('IS_AUTHENTICATED_FULLY') || $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED') ){
            // authenticated REMEMBERED, FULLY will imply REMEMBERED (NON anonymous)
            $isLoggedIn = true;
        }
        
        $user = $this->getUser();
       
        return $this->render('AdminBaseBundle:Page:login_widget.html.twig', array(
            // last username entered by the user
            'user' => $user,
            'logged_in' => $isLoggedIn,
            'show_login_form' => $showLoginForm
        ));
    }
    
}
?>
