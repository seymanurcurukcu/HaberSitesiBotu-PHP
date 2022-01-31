<?php 
try  {
	$baglanti = new PDO("mysql:host=localhost;dbname=haberbot;charset=utf8", "root","");
	$baglanti->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	
} catch (PDOException $e) {
	die($e->getMessege());
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>HABER BOTUMUZ</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<style>
body {
height:100%;
width: 100%;
position:absolute;
	
}
</style>

  </head>

  <body>
  
  <div class="container-fluid h-100">
  
  
  
  		<div class="row h-100">
        		<div class="col-lg-3 border-right text-center">
                
                		<div class="row">                      
                        
                        <div class="col-lg-12 bg-danger text-white"><h4>TÜM HABERLER</h4></div>
                        
                        <div class="col-lg-12">
                          <?php  
                          $basliklar=array();
                           $sorgula=$baglanti->prepare("select * from localtablo");
                           $sorgula->execute();
                           while ($sonuc=$sorgula->fetch(PDO::FETCH_ASSOC)) {
                             $basliklar[]=$sonuc["baslik"];
                           }


                          if (@$_POST["ilkbuton"]=="") {
                             
                          
                              $veri=file_get_contents("http://localhost/HaberSitesiBotuYapimi/VeriAlinanHaberSitesi/");
                              $desen='@<div class="col-lg-4 col-md-4 col-sm-4 mt-2">(.*?)</div>@si';
                              preg_match_all($desen,$veri,$linkler);
                              $toplamSayi=count($linkler[1]);

                            for ($i=0; $i < $toplamSayi; $i++) { 
                              $desen3='@<a id="haberlink" href="(.*?)">(.*?)</a>@si';
                              preg_match_all($desen3,$linkler[1][$i],$linkvebaslik);  
                              $bas=$linkvebaslik[2][0];
                             

                              if (in_array($linkvebaslik[2][0],$basliklar)) {
                                
                               continue;
                              }


                              echo '<form action="" method="post">';
                              $desen2='@<img class="card-img-top" src="(.*?)" height="200">@si';
                              preg_match_all($desen2,$linkler[1][$i],$resim);
                              echo '<img src="http://localhost/HaberSitesiBotuYapimi/VeriAlinanHaberSitesi/'.$resim[1][0].'"
                              width="250" height="150">';
                              echo "<br>";
 
                              echo '<input type="hidden" class="form-control m-2" name="res[]" value="'.$resim[1][0].'">';
                             
                              
                              echo '<input type="text" class="form-control m-2" name="baslik[]" value="'.$linkvebaslik[2][0].'">';
 
                              //içeriye giriyoruz
                              $detay=file_get_contents("http://localhost/HaberSitesiBotuYapimi/VeriAlinanHaberSitesi/".$linkvebaslik[1][0]);
                              $desen4='@id="habericerik">(.*?)</div>@si';
                              preg_match_all($desen4,$detay,$detayagirdim);
                              echo '<textarea name="icerik[]" class="form-control" rows="5">'.$detayagirdim[1][0].'</textarea>';
                             
                             
                              echo '<input type="submit" name="ilkbuton" class="btn btn-success m-2" value="KAYDET">
                            </form>';
                            echo "<hr>";
                            }
                            
                         }
                         else {
                           /*foreach ($_POST["baslik"] as $key => $value) {
                            echo $value."<br>";
                            echo $_POST["icerik"][$key]."<br>";
                            echo $_POST["res"][$key]."<br><hr>";
                           }*/
                          $ekle=$baglanti->prepare("insert into localtablo(baslik,icerik,resim) values (:baslik,:icerik,:res)");
                          foreach ($_POST["baslik"] as $key => $value) {
                            $resimcek=file_get_contents("http://localhost/HaberSitesiBotuYapimi/VeriAlinanHaberSitesi/".$_POST["res"][$key]);
                            $uzantiyaulasiyorum=explode(".",$_POST["res"][$key]);
                            $sonuzanti=".".$uzantiyaulasiyorum[count($uzantiyaulasiyorum)-1];
                            $asama1=str_shuffle("suygeur");
                            $asama2=mt_rand(0,145222);
                            $dosyaadi="res/".$asama1.$asama2.$sonuzanti;
                          
                            $indir=fopen($dosyaadi,"a+");
                            fwrite($indir,$resimcek);
                            fclose($indir);
                           
                           
                            $ekle->execute(array(':baslik'=>$value,':icerik'=>$_POST["icerik"][$key],':res'=>$dosyaadi));
                          
                           }
                           echo '<div class="alert alert-success m-2">Eklemeler Başarılı </div>';
                         }
                          ?>


                        </div>
                        
                        </div>
                
               
              
                
        		</div>
                
                <div class="col-lg-3 border-right text-center">
                
                		<div class="row">                      
                        
                        <div class="col-lg-12 bg-danger text-white"><h4>SON DAKİKALAR</h4></div>
                        
                        <div class="col-lg-12">
                          <?php
                            $son=file_get_contents("http://localhost/HaberSitesiBotuYapimi/VeriAlinanHaberSitesi/");
                            $desen5='@id="soneklenenstil"(.*?)</div>@si';
                            preg_match_all($desen5,$son,$sondakika);
                            $toplamSayi2=count($sondakika[1]);

                            for ($i=0; $i < $toplamSayi2; $i++) { 
                                
                           

                            $desen6='@ <a href="(.*?)">(.*?)</a>@si';
                            preg_match_all($desen6,$sondakika[1][$i],$Link);
                            echo '<input type="text" class="form-control m-2" name="baslik[]" value="'.$Link[2][0].'">';

                            
                             //içeriye giriyoruz
                            $detay2=file_get_contents("http://localhost/HaberSitesiBotuYapimi/VeriAlinanHaberSitesi/".$Link[1][0]);
                            $desen7='@id="habericerik">(.*?)</div>@si';
                            preg_match_all($desen7,$detay2,$detayagirdim);
                            echo '<textarea name="icerik" class="form-control" rows="5">'.$detayagirdim[1][0].'</textarea>';
                            echo "<br>";
                            
                            //resim
                            $desen8='@<img class="card-img-top" src="(.*?)" height="200">@si';
                            preg_match_all($desen8,$detay2,$resim2);
                            echo '<img src="http://localhost/HaberSitesiBotuYapimi/VeriAlinanHaberSitesi/'.$resim2[1][0].'"
                            width="250" height="150">';
                            echo "<br>";
                            echo "<hr>";
                            }
                           ?>

                        </div>
                        
                        </div>
                
        		</div>
                
                <div class="col-lg-3 border-right text-center">
                
                		<div class="row">                      
                        
                        <div class="col-lg-12 bg-danger text-white"><h4>SON EKLENENLER</h4></div>
                        
                        <div class="col-lg-12">

                         <?php
                            $son=file_get_contents("http://localhost/HaberSitesiBotuYapimi/VeriAlinanHaberSitesi/");
                            $desen5='@id="yeneklenenstil"(.*?)</div>@si';
                            preg_match_all($desen5,$son,$sondakika);
                            $toplamSayi2=count($sondakika[1]);

                            for ($i=0; $i < $toplamSayi2; $i++) { 

                            $desen6='@ <a href="(.*?)">(.*?)</a>@si';
                            preg_match_all($desen6,$sondakika[1][$i],$Link);
                            echo '<input type="text" class="form-control m-2" name="baslik[]" value="'.$Link[2][0].'">';
 
                            
                             //içeriye giriyoruz
                            $detay2=file_get_contents("http://localhost/HaberSitesiBotuYapimi/VeriAlinanHaberSitesi/".$Link[1][0]);
                            $desen7='@id="habericerik">(.*?)</div>@si';
                            preg_match_all($desen7,$detay2,$detayagirdim);
                            echo '<textarea name="icerik" class="form-control" rows="5">'.$detayagirdim[1][0].'</textarea>';
                            echo "<br>";
                            
                            //resim
                            $desen8='@<img class="card-img-top" src="(.*?)" height="200">@si';
                            preg_match_all($desen8,$detay2,$resim2);
                            echo '<img src="http://localhost/HaberSitesiBotuYapimi/VeriAlinanHaberSitesi/'.$resim2[1][0].'"
                            width="250" height="150">';
                            echo "<br>";
                            echo "<hr>";
                            }
                           ?>
                        </div>
                        
                        </div>
                
                
        		</div>
                
                <div class="col-lg-3 text-center">
                
                		<div class="row">                      
                        
                        <div class="col-lg-12 bg-danger text-white"><h4>KATEGORİYE GÖRE</h4></div>
                        
                        <div class="col-lg-12">
                          <?php
                                $kategori=file_get_contents("http://localhost/HaberSitesiBotuYapimi/VeriAlinanHaberSitesi/");
                                $desen15='@<li class="nav-item">(.*?)</li>@si';
                                preg_match_all($desen15,$kategori,$kat);
                                $toplamSayi3=count($kat[1]);
                                echo '<form action="" method="post"> 
                                <select name="katid" class="form-control m-2">
                                <option value="0">Seç</option>';

                                for ($i=0; $i < $toplamSayi3; $i++) { 
                                $desen16='@<a class="nav-link" href="(.*?)">(.*?)</a>@si';
                                preg_match_all($desen16,$kat[1][$i],$veriler);
                                $id=str_replace("index.php?katid=","",$veriler[1][0]);
                                echo '<option value="'.$id.'">'.$veriler[2][0].'</option>';  
                                           
                                }
                               
                                echo '</select>
                                <input type="submit" name="btn" class="btn btn-primary">
                                </form>';
                              
                                if (@$_POST["katid"]!="") {
                                    $veri10=file_get_contents("http://localhost/HaberSitesiBotuYapimi/VeriAlinanHaberSitesi/index.php?katid=".$_POST["katid"]);
                                    $desen20='@<div class="col-lg-4 col-md-4 col-sm-4 mt-2">(.*?)</div>@si';
                                    preg_match_all($desen20,$veri10,$linkler);
                                    $toplamSayi=count($linkler[1]);

                                  for ($i=0; $i < $toplamSayi; $i++) { 
                                    $desen2='@<img class="card-img-top" src="(.*?)" height="200">@si';
                                    preg_match_all($desen2,$linkler[1][$i],$resim);
                                    echo "<br>";
                                    echo '<img src="http://localhost/HaberSitesiBotuYapimi/VeriAlinanHaberSitesi/'.$resim[1][0].'"
                                    width="250" height="150">';

                                    $desen3='@<a id="haberlink" href="(.*?)">(.*?)</a>@si';
                                    preg_match_all($desen3,$linkler[1][$i],$linkvebaslik);   
                                    echo '<input type="text" class="form-control m-2" name="baslik[]" value="'.$linkvebaslik[2][0].'">';

                                      
                                    
                                    //içeriye giriyoruz
                                    $detay=file_get_contents("http://localhost/HaberSitesiBotuYapimi/VeriAlinanHaberSitesi/".$linkvebaslik[1][0]);
                                    $desen4='@id="habericerik">(.*?)</div>@si';
                                    preg_match_all($desen4,$detay,$detayagirdim);
                                    echo '<textarea name="icerik" class="form-control" rows="5">'.$detayagirdim[1][0].'</textarea>';
                                    echo "<br>";
                                    echo "<hr>";
                                  }
                                
                                }

                          ?>
                        </div>
                        
                        </div>
                
        		</div>
        
        
        </div>
  
  
  
  
  </div>
  
  
 
  

  
  

  </body>

</html>
