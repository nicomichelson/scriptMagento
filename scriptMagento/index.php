<?php

                // Definimos la clase
            class Buscar{
                
                private $nickname;
                private $json2;
                
                
                // Constructor
                public function __construct($nickname){
                   
                    $this->validarJson($nickname);
                }
                
                //metodos

                private function validarJson($nickname){

                    //$json = file_get_contents('https://api.mercadolibre.com/sites/MLA/search?nickname='.$nickname);
                    $json = file_get_contents('https://api.mercadolibre.com/sites/MLA/search?seller_id=127422368&offset=0&limit=1');


                    $data = json_decode($json);
                    $this->armarData($data);

                }

                public function armarData($data){


                    $producto = array();

                    foreach($data->results as $k ):
                           
                        $item = $this->devolverItem($k->id);

                        $sal=['id'                  => $k->id, 
                            'title'                 => $k->title,
                            'price'                 => $k->price,
                            'currency_id'           => $k->currency_id, 
                            'available_quantity'    => $k->available_quantity, 
                            'sold_quantity'         => $k->sold_quantity, 
                            'listing_type_id'       => $k->listing_type_id, 
                            'stop_time'             => $k->stop_time,
                            'condition'             => $k->condition, 
                            'permalink'             => $k->permalink, 
                            'thumbnail'             => $k->thumbnail, 
                            'accepts_mercadopago'   => $k->accepts_mercadopago, 
                            'category_id'           => $k->category_id, 
                            'official_store_id'     => $k->official_store_id,
                            
                        ]; 
                        array_push($sal, $item);
                        array_push($producto, $sal);
                        endforeach;

                         $this->json2 = json_encode($producto);
                    
                    
                    
                }

                private function devolverItem($item){

                    $json = file_get_contents('https://api.mercadolibre.com/items/'.$item);
                    $data = json_decode($json);

                    $salItem=[
                            'precio'            =>  $data->price,
                            'precio_base'       =>  $data->base_price,
                            'precio_original'   =>  $data->original_price,  
                            'miniatura'         =>  $data->secure_thumbnail
                    ];

                    return $salItem;

                }

                public function mostraJson(){
                    
                    return $this->json2;
                }

                
            }
            ?>
            
            <!-- salida por pantalla -->
            <!DOCTYPE HTML>
            <html lang="es">
            <head>
                <meta charset="utf-8"/>
                <title>Cosme Fulanito</title>
            </head>
            <body>
            
            <?php
            
            $nickname ='LAPARFUMERIE';

            

            //instancio la clase y llamo al metodo mostrarjson
            //en este caso muestra un array con los datos d los productos
            //de un vendedor 
            $resultado = new Buscar($nickname);
            
            
            echo $resultado->mostraJson();
            
            
            ?>
            
            </body>
            </html>


?>