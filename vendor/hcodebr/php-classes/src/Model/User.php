<?php 

  // Indica o caminho onde a Classe ira pegar os dados
  namespace Hcode\Model;  

  use \Hcode\DB\Sql;
  use \Hcode\Model;

  Class User extends Model 
  {

    const SESSION = "User";

  	public static function login($login, $password)
  	{

  		$sql = new Sql();

  		$results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array(":LOGIN"=>$login));

  		if (count($results) === 0) 
  		{
  			throw new \Exception("Usuário inexistente ou senha inválida.");

  		}

  		$data = $results[0];

  		// funcao password_verify verifica se o Hass bateu com a senha retornado true
  		if(password_verify($password, $data["despassword"])=== true)
  		{

			$user = new User();

 			$user->setData($data);

      $_SESSION[User::SESSION] = $user->getValues();

      return $user;
 		
  		} 
  		else 
      {
			throw new \Exception("Usuário inexistente ou senha inválida.");
  		}

    } 

  public static function verifyLogin($inadmin = true)
  {
    if (
      !isset($_SESSION[User::SESSION])
      ||
      !$_SESSION[User::SESSION]
      ||
      !(int)$_SESSION[User::SESSION]["iduser"] > 0
      ||
      (bool)$_SESSION[User::SESSION]["inadmin"] !== $inadmin
    ) {
     
      header("Location: /admin/login");
      exit;
    }
      
    }

    public static function logout() 
    {

        $_SESSION[User::SESSION] = NULL;

    }

}

?>
