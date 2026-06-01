<?php
Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_UploadVideo extends Elotech_Db_Table_Abstract {

    protected $_name = 'upload_video';
    protected $_primary = 'upv_codigo';   
    public function salvar(array $data){
        $this->addRealName(array("upv_descricao" => "descrição","upv_titulo"=>"titulo"));    
       /* $email  = 'name@example.com';
$domain = strstr($email, 'vvideo');
echo $domain."<br>"; // prints @example.com

$user = strstr($email, '@'); // A partir do PHP 5.3.0
echo $user; // prints name
die();*/
        $this->diretorio($_SESSION[root].$_SESSION[modulo]."zf\\public\\videos");
        
        die();
        $this->emptyToUnset($data);
        return parent::salvar($data);
    }
    public function retornaVideo(){
        $path = $_SESSION[root].$_SESSION[modulo]."zf\\public\\videos";
         if ($dir = opendir($path)) {
            
            $array = array();
            while (false !== ($file = readdir($dir))) {
                if (is_dir($path."/".$file)) {                
                    if ($file != '.' && $file != '..') { 
                       // echo '<li><b>' . $file . '</b></li><ul>';
                       //$this->diretorio($path."/".$file);
                       // echo '</ul>';
                        //$total_pastas++;
                    }
               }else {                    
                       array_push($array, $file);
                        
                    
               }
            }
            $arrayNovo = $this->array_random($array);
             //echo "<pre>".print_r(,1); die();
           // $arrayNovo2 = array_rand($array, 1);
           return $arrayNovo;
            //die();
            closedir($dir);
        }
    }
    public function array_random($arr, $num = 1) {
        shuffle($arr);

        $r = array();
        for ($i = 0; $i < $num; $i++) {
            $r[] = $arr[$i];
        }
        return $num == 1 ? $r[0] : $r;
    }
    public function diretorio($path) {
       
        global $tamanho_arquivo, $tamanho_total, $total_pastas;
        if ($dir = opendir($path)) {
            $tamanho_arquivo = 0;
            while (false !== ($file = readdir($dir))) {
                if (is_dir($path."/".$file)) {                
                    if ($file != '.' && $file != '..') { 
                       // echo '<li><b>' . $file . '</b></li><ul>';
                       $this->diretorio($path."/".$file);
                       // echo '</ul>';
                        $total_pastas++;
                    }
               }else { 
                    $tab = " ";
                     //die($path);
                    //$filesize = $tab . '(' . filesize ($path.'/'.$file) . ' kb)';
                    if(strstr(!$file, 'vvideo')){ 
                        die('ssasdasdsa');
                    }
                        echo  $file ."<br>";
                         $tamanho_total = $tamanho_total + filesize ($path.'/'.$file);
                         $tamanho_arquivo++;
                    
               }
            }
            echo $tamanho_arquivo;
            closedir($dir);
        }
    }
     public function geraThumbs($arquivos=FALSE,$tamanhox=FALSE,$tamanhoy=FALSE,$pasta=FALSE){
            
            foreach($arquivos as $arquivo){
                $caminnhoFotoOriginal = $_SESSION[root].$_SESSION[modulo]."raiox\\server\\php\\files\\$arquivo->arquivos";
                $caminnhoFotoOriginal = str_replace("/", "\\", $caminnhoFotoOriginal);
                
                $caminnhoFotoMiniatura = $_SESSION[root].$_SESSION[modulo]."raiox\\server\\php\\files\\$pasta\\$arquivo->arquivos";
                $caminnhoFotoMiniatura = str_replace("/", "\\", $caminnhoFotoMiniatura);
                $formato = explode(".",$arquivo->arquivos);
                if(in_array("jpg",$formato) || in_array("jpeg",$formato)){
                    $imagemOriginal = imagecreatefromjpeg( $caminnhoFotoOriginal );
                }else if(in_array("png",$formato)){
                    $imagemOriginal = imagecreatefrompng( $caminnhoFotoOriginal );
                }else if(in_array("gif",$formato)){
                    $imagemOriginal = imagecreatefromgif( $caminnhoFotoOriginal );
                }
                $larguraOriginal = imagesx( $imagemOriginal );
                $alturaOriginal = imagesy( $imagemOriginal );

                $imagemMiniatura = imagecreatetruecolor($tamanhox, $tamanhoy);
                imagecopyresampled($imagemMiniatura, $imagemOriginal, 0, 0, 0, 0, $tamanhox, $tamanhoy, $larguraOriginal, $alturaOriginal);
                if(in_array("jpg",$formato) || in_array("jpeg",$formato)){
                    imagejpeg( $imagemMiniatura, $caminnhoFotoMiniatura);
                }else if(in_array("png",$formato)){                    
                    imagepng( $imagemMiniatura, $caminnhoFotoMiniatura);
                }else if(in_array("gif",$formato)){
                    imagegif( $imagemMiniatura, $caminnhoFotoMiniatura);
                }
            }               
        }
        
        public function limpaDir($diretorios=FALSE){
            $caminho = $_SESSION[root].$_SESSION[modulo]."zf\\public\\videos";
            $caminho = str_replace("/", "\\", $caminho);
            $handle = opendir($caminho);            
            $diretorio = dir($caminho);
             echo "Lista de Arquivos do diretório '<strong>".$caminho."</strong>':<br />";    
            while($arquivo = $diretorio -> read()){
               echo "<a href='".$caminho.$arquivo."'>".$arquivo."</a><br />";
            }
            
            
            while(($file = readdir($handle)) !== false){
                if(!in_array($file,$itensNaoPermitidos)){
                    unlink($caminho."\\".$file);
                }
            }

            foreach($diretorios as $diretorio){
                $handle = opendir($caminho."\\".$diretorio);
                while(($file = readdir($handle)) !== false){
                    if(!in_array($file,$itensNaoPermitidos)){
                        unlink($caminho."\\".$diretorio."\\".$file);
                    }
                }
            }
        }
  
}

