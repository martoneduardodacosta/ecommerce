
<?
/// ******************************************** ///
/// Créditos Pedro Young Duk Son *************** ///
/// Fire Desenvolvimento WEB ******************* ///
/// E-mail: pyds@hotmail.com ******************* ///
/// ******* pedro@firedw.com.br **************** ///
/// ******************************************** ///

    $arquivo = fopen ("adiciona.txt" , "r+" );
    $conta = fread($arquivo, filesize("adiciona.txt"));
    fclose($arquivo);
    $conta +=1;
    $arquivo = fopen("adiciona.txt","w+");
    fputs($arquivo, $conta);
    fclose($arquivo);
?>
<?
    include("adiciona.txt");
?>

