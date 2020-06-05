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
                        
                        $item = $this->devolverItem($k->id,$precio_original);
                       
                        $precios_calculados=[//arma un array con precios calculados por cada producto
                            'total_comision'                 =>  $comision,
                            'gananciaML'                     =>  $gananciaML,
                            'gastos_envio_vendedror'         =>  $this->gastoEnvioVendedor(),
                            'precio_original'                =>  $precio_original

                        ];
                       
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
                            'item'                  => $item ,
                            'precio_calculados'     => $precios_calculados
                            
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
                            'precio_original'   =>  $precio_original, //este campo se actualiza porq viene vacio desde la api 
                            'miniatura'         =>  $data->secure_thumbnail
                    ];

                    return $salItem;

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
            
            <!-- salida por pantalla -->
            <!DOCTYPE HTML>
            <html lang="es">
            <head>
                <meta charset="utf-8"/>
                <title>Cosme Fulanito</title>
            </head>
            <body>
            
                <!--Formulario-->
                <form action="index.php" method="POST">
                    <p>Nickname: <input type="text" name="nickname" /></p>
                    
                    <p><input type="submit" name="submit" value="Enviar"/></p>
                </form>



            <?php
            
            if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])){
                 
                if(!empty($_POST['nickname'])){
                    $nickname =$_POST['nickname'];//no importa si es camelcase anda igual

                    

                    //instancio la clase y llamo al metodo mostrarjson
                    //en este caso muestra un array con los datos d los productos
                    //de un vendedor 
                    $resultado = new Productos($nickname);
                    $productos = $resultado->mostraProductos();
                    
                    //echo var_dump($productos);
                    //print_r($productos);

                        foreach($productos as $producto  ):
                            echo "<p> <strong>Producto</strong> </p>";
                            foreach($producto as $key => $value ):

                                switch($key){
                                    case ($key == "id"):
                                        echo "<p> <strong>$key ...  $value</strong> </p>";
                                    break;
                                    case ($key == "item" || $key == "precio_calculados"):
                                        echo "<p> <strong>$key</strong> </p>";
                                        foreach($value as $items => $item):
                                            echo "<p> $items ... $item</p>";
                                        endforeach;
                                    break;
                                    default:
                                        echo "<p> $key ...  $value </p>";

                                }
                                
                            endforeach;
                            echo "<br>----------------</br>";
                        endforeach;

                }else{
                        echo 'campo vacio';
                
                }
            }    
                    
                    
                    
                        
            
            ?>
            
            </body>
            </html>


