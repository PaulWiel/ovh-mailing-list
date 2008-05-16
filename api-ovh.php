<?php

  /* --------------------------------------------------------------
  
       Fonction OVH 
			 
       Version 3.510.00 
       
       Copyright (c) 2004-2005, Nicolas MERLET - www.merletn.org 
			 
       Le present script est la pleine et entiere propriete 
       intellectuelle de l'auteur. 
			 
       Tous les droits sont strictement reserves a l'exception 
       d'un droit d'usage, y compris commercial, qui vous est 
       gracieusement accorde. 
			 
     --------------------------------------------------------------
			 
       Nicolas MERLET 
       IT Consulting & Web Development 
       http://www.merletn.org 
			
       Phone : +33 (0) 674.520.959 
       Email : nicolas@merlet.info 
         ICQ : 239-803-142 
         MSN : msn@merletn.org 
			
       SIRET : 481 258 242 000 17 
	   
     --------------------------------------------------------------
       
       Utilisation - Liste des parametres : 
       
         $api    : Fonction de l'API Manager OVH 
         $ssid   : ID de la session SOAP 
         $params : Parametres de la fonction de l'API (array) 
				 
       Vous pouvez ommettre $ssid ou $params selon vos besoins 
       et leur ordre respectif n'a aucune d'importance ! 
       
       Seul le parametre $api doit etre passe en premier... 
       
       La fonction renvoie le hash decrit dans la doc de l'API 
       en cas de succes ou 'false' si une erreur se produit. 
       
       Cette fonction detecte et resoud automatiquement les 
       problemes de sessions ('too many sessions') ! 
       
       La liste des fonctions de l'API est disponible ici : 
         http://www.verot.org/ovhapi/
				 
  -------------------------------------------------------------- */
  
  
  function OVH ()
  {
      global $ovh_status ;
    
    
    // -- Detection des arguments --
    
    
      $args = func_get_args () ;
      if ( sizeof ( $args ) < 2 ) return false ;
    
      $api = $args[0] ;
      if ( ! is_string ( $api ) ) return false ;

      $ssid = $args[1] ;
      if ( sizeof ( $args ) > 2 ) { $params = $args[2] ; } else { $params = null ; }
    
      if ( is_array ( $ssid ) ) list ( $params , $ssid ) = array ( $ssid , $params ) ;
      if ( is_array ( $ssid ) || ( ! is_array ( $params ) && ! is_null ( $params ) ) ) return false ;
    
      if ( ! is_null ( $ssid   ) ) $full_params[] = $ssid   ;
      if ( ! is_null ( $params ) ) $full_params[] = $params ;
    
    
    // -- Temporisation --
      
      
      $delay = 250 ;
      
      list ( $usec , $sec ) = explode ( " " , microtime ( ) ) ;
      $t =  round ( ( ( ( float ) $usec ) * 1000 + ( ( float ) $sec ) * 1000 ) ) ; 
      
      static $time = 0 ;
      if ( $time == 0 ) $time = $t - $delay ;
      
      if ( ( $t - $time ) < $delay ) { usleep ( $delay - ( $t - $time ) ) ; }
      
      $time = $t ;
          
	  
    // -- Initialisation du client SOAP --
    
    
      static $soap_client , $options ;
      static $init = false ;
    
      $return = false ;
    
      if ( ! $init )
      {
        require ( "SOAP/Client.php" ) ;
      
        $soap_client = new SOAP_Client ( "http://ovh.com:1663" ) ;
        $soap_client -> setEncoding ( "UTF-8" ) ;
      
        $options = array ( "namespace" => "urn:manager" , "trace" => 1 , "timeout" => 10 ) ;
      
        $init = true ;
      }
      
      
    // -- Verrouillage --
    
    
      if ( $api == "ClearNicSessions" )
      {
        if ( defined ( "OVHCLEARNIC" ) )
        {
          return $return ;
        }
        else
        {
          define ( "OVHCLEARNIC" , true ) ;
        }
      }
      
      
    // -- Execution de la fonction API --
    
    
      $result = $soap_client -> call ( $api , $full_params , $options ) ;
      
      if ( ! PEAR::isError ( $result ) )
      {
        $result = get_object_vars ( $result ) ;
				
				
    // -- Conversion des objets en tableaux --
    
    
        if ( ! function_exists ( 'OVH_OBJ_CONV' ) )
        {
          function OVH_OBJ_CONV ( $r )
          {
            if ( is_object ( $r ) ) { $r = get_object_vars ( $r ) ; }
            if ( is_array  ( $r ) ) { $r = array_map ( 'OVH_OBJ_CONV' , $r ) ; }
            return $r ;
          }
        }
        $result = array_map ( 'OVH_OBJ_CONV' , $result ) ;
	
	
    // -- Traitement du resultat --
    
    
        if ( ! isset ( $result["status"] ) && isset ( $result["value"] ) )
        {
          if ( is_numeric ( $result["value"] ) ) { $ovh_status = $result["value"]  ; }
          else                                   { $ovh_status = $result["status"] ; }
        }
        else { $ovh_status = $result["status"] ; }
	
        if ( isset ( $result["ie"]  ) ) $ovh_status .= "/"   . $result["ie"]  ;
        if ( isset ( $result["msg"] ) ) $ovh_status .= " - " . $result["msg"] ;
	
        switch ( $result["status"] )
        {
          case 100 : 
	  
            $return = $result ; break ;
	  
          case 304 :
	    
            if ( ovh ( "ClearNicSessions" , $params ) !== false )
            {
              $return = ovh ( $api , $ssid , $params ) ;
            }	    
            break ;
        }
      }

      return $return ;
  }

?>
