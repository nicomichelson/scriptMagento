<?php

                // Definimos la clase
            class Productos{
                
                //constantes
                const PORCENTAJE_COMISION = 13;
                const GASTOS_ENVIO = 284;
                
                
                private $nickname;
                private $datos_productos;
                
                
                
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


                    $productos = array();

                    foreach($data->results as $k ):
                         
                        
                        $importe_publicacion = $k->price;
                        $comision = $this->totalComision($k->price);
                        $gananciaML = $this->totalComision($k->price) - $this->gastoEnvioVendedor();
                        $precio_original =$importe_publicacion - $comision - $this->gastoEnvioVendedor();
                        
                        $item['item'] = $this->devolverItem($k->id,$precio_original);

                        $producto['pruducto']=['id' => $k->id, 
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
                        
                        $precios_calculados['precio_calculado']=[
                            'total_comision'                 =>  $comision,
                            'gananciaML'                     =>  $gananciaML,
                            'gastos_envio_vendedror'         =>  $this->gastoEnvioVendedor(),
                            'precio_original'                =>  $precio_original

                        ];
                        
                        array_push($producto, $item,$precios_calculados);
                        array_push($productos, $producto);
                        
                    endforeach;

                         //$this->datos_productos = json_encode($productos);
                         $this->datos_productos = $productos;
                    
                    
                }

                private function devolverItem($item,$precio_original){

                    $json = file_get_contents('https://api.mercadolibre.com/items/'.$item);
                    $data = json_decode($json);
                    
                    //$precio_original = $data->price - $comision - $this->gastoEnvioVendedor();
                    $salItem=[
                            'precio'            =>  $data->price,
                            'precio_base'       =>  $data->base_price,
                            'precio_original'   =>  $precio_original, //este campo se actualiza porq viene vacio desde la api 
                            'miniatura'         =>  $data->secure_thumbnail
                    ];

                    return $salItem;

                }

                private function gastoEnvioVendedor(){
                   return self::GASTOS_ENVIO/2;
                }

                private function totalComision($precio){
                    return ($precio * self::PORCENTAJE_COMISION)/100;
                }

                

                public function mostraJson(){
                    
                    return $this->datos_productos;
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
            $resultado = new Productos($nickname);
            
            
           // echo var_dump($resultado->mostraJson());
            print_r($resultado->mostraJson());
            
            ?>
            
            </body>
            </html>


?>