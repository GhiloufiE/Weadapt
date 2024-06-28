<?php

class FEP_Email_Pipe {


    public function __construct(){
        if ( 'piping' !== fep_get_option( 'ep_enable' ) ) {
			return;
		}
		$ep = new FEP_Email_Parser();
		$ep->readSTDIN();
		$ep->decode();

		new FEP_Email_Process( $ep );

    }

}
