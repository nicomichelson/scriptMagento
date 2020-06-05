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

                private function validarJson($nickname){//valida una api

                    $json = file_get_contents('https://api.mercadolibre.com/sites/MLA/search?nickname='.$nickname.'&offset=0&limit=1');
                    //$json = file_get_contents('https://api.mercadolibre.com/sites/MLA/search?seller_id=127422368&offset=0&limit=5');
                    //LAPARFUMERIE

                    $data = json_decode($json);
                    $this->armarData($data);

                }

                public function armarData($data){//arma un array de salida con productos y sus datos


                    $productos = array();

                    foreach($data->results as $k ):
                         
                        
                        $importe_publicacion = $k->price;
                        $comision = $this->totalComision($k->price);
                        $gananciaML = $this->totalComision($k->price) - $this->gastoEnvioVendedor();
                        $precio_original =$importe_publicacion - $comision - $this->gastoEnvioVendedor();
                        
                        //obtengo item completo por cada publicacion y datos de las imagenes de cada publicacion
                        $item = $this->devolverItem($k->id,$precio_original);
                        $imagen = $this->devolverImagen($k->id);
                        
                        
                       
                        $producto=[ 'id' => $k->id, 
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
                           
                            // items
                            'precioItem'            =>$item['precio'],
                            'precioBaseItem'        =>$item['precio_base'],
                            'precioOriginalItem'    =>$item['precio_original'],//viene null de la api
                            'miniaturaItem'         =>$item['miniatura'],
                            //precios calculados
                            'total_comision'        =>  $comision,
                            'gananciaML'            =>  $gananciaML,
                            'gastos_envio_vendedror' =>  $this->gastoEnvioVendedor(),
                            'precio_original'        =>  $precio_original,
                            'imagenesItem'           =>  $imagen
                            
                        ]; 
                        
                        
                        
                        array_push($productos, $producto);
                        
                    endforeach;

                         
                         $this->datos_productos = $productos;
                    
                    
                }

                private function devolverItem($item,$precio_original){//devuelve el item de un producto

                    $json = file_get_contents('https://api.mercadolibre.com/items/'.$item);
                    $data = json_decode($json);
                    
                    
                    $salItem=[
                            'precio'            =>  $data->price,
                            'precio_base'       =>  $data->base_price,
                            'precio_original'   =>  $data->original_price, //viene null de la api
                            'miniatura'         =>  $data->secure_thumbnail,
                            'imagenes'          =>  $data->pictures
                    ];

                    return $salItem;

                }

                private function devolverImagen($imagen){//devuelve las imagenes de cada publicacion
                    $json = file_get_contents('https://api.mercadolibre.com/items/'.$imagen);
                    $data = json_decode($json);

                    $imagen = $data->pictures;
                    return $imagen;

                }

                private function gastoEnvioVendedor(){
                   return self::GASTOS_ENVIO/2;
                }

                private function totalComision($precio){//calcula la comision de un producto
                    return ($precio * self::PORCENTAJE_COMISION)/100;
                }

                

                public function mostraProductos(){//devuelve un array con productos
                    
                    return $this->datos_productos;
                }

                
               
                
            }
            ?>