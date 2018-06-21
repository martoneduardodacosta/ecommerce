<?php 

  // Indica o caminho onde a Classe ira pegar os dados
  namespace Hcode\Model;  

  use Rain\Tpl;
  use \Hcode\DB\Sql;
  use \Hcode\Mailer;
  use \Hcode\Model;

  Class User extends Model 
  {

    // Constantes de chaves 
    const SESSION = "User";
    const SECRET = "HcodePhp7_Secret";
    


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

    public static function listAll() {

      $sql = new Sql();

      // b = ponteiro para o arquivo tb_persons
      // a = ponteiro para o arquivo tb_users
      return $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson");
    }

    // Salva atributos da tela no BD
    public function save() {

        $sql = new Sql();

        $results = $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
          ":desperson"=>$this->getdesperson(),
          ":deslogin"=>$this->getdeslogin(),
          ":despassword"=>password_hash($this->getdespassword(),PASSWORD_DEFAULT),
          ":desemail"=>$this->getdesemail(),
          ":nrphone"=>$this->getnrphone(),
          ":inadmin"=>$this->getinadmin(),
          
        )); 

        $this->setData($results[0]);

    }    


    public function get($iduser)
    {

        $sql = new Sql();

        $results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser", array(":iduser"=>$iduser
      ));

        $this->setData($results[0]);

    }

    public function update(){


      $sql = new Sql();

      $results = $sql->select("CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
          "iduser"=>$this->getiduser(),
          ":desperson"=>$this->getdesperson(),
          ":deslogin"=>$this->getdeslogin(),
          ":despassword"=>$this->getdespassword(),
          ":desemail"=>$this->getdesemail(),
          ":nrphone"=>$this->getnrphone(),
          ":inadmin"=>$this->getinadmin(),
          
        )); 

    }


    public function delete(){

      $sql = new Sql();

      $sql->query("CALL sp_users_delete(:iduser)", array(
        ":iduser"=>$this->getiduser()

      ));   
    }
  
    // Função para tratar a alteração de senha de usuário
    public static function getForgot($email)
    {

      $sql = new Sql();

      $results = $sql->select("
        SELECT * 
        FROM tb_persons a 
        INNER JOiN tb_users b USING(idperson)
        WHERE a.desemail = :email;        
        ", array(
          ":email"=>$email
      ));

      // email nao cadastrado no banco de dados

      if (count($results) === 0)
      {
         throw new \Exception("Não foi possivel recuperar a senha.");
      } 
      else 
      {

          $data = $results[0];

          $results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(":iduser"=>$data["iduser"],
            ":desip"=>$_SERVER["REMOTE_ADDR"]
       
        ));


        if (count($results2) === 0)
        {
            throw new \Exception("Não foi possivel recuperar a senha.");
        }      
        else
        {

            $dataRecovery = $results2[0];

            //$code = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, User::SECRET, $dataRecovery["idrecovery"], MCRYPT_MODE_ECB));

            $code = $dataRecovery["idrecovery"];

            $link = "http://www.hcodecommerce.com.br/admin/forgot/reset?code=$code";

            $mailer = new Mailer($data["desemail"], $data["desperson"], "Redefinir Senha", "forgot", array(
              "name"=>$data["desperson"],
              "link"=>$link
            ));


            $mailer->send();

            return $data;

        }

      }

    }
   
    public static function validForgotDecrypt($code)
    {

      //$idrecovery = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, User::SECRET, base64_decode($code), MCRYPT_MODE_ECB);

      //$idrecovery = $code;

      $idrecovery = $code;

      $sql = new Sql();

      $results = $sql->select("
         SELECT * 
         FROM tb_userspasswordsrecoveries a
         INNER JOIN tb_users b USING(iduser)
         INNER JOIN tb_persons c USING(idperson)
         WHERE
            a.idrecovery = :idrecovery
            AND
            a.dtrecovery IS NULL
          AND
            DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW();",
            array(":idrecovery" => $idrecovery
          ));  
     
        if (count($results) === 0)
        {
          throw new \Exception("Não foi possivel recuperar senha");
        } 
        else 
        {

          return $results[0];

        }

    }

    public static function setForgotUsed($idrecovery)
    {
      $sql = new Sql();

      $sql->query("UPDATE tb_userspasswordsrecoveries SET dtrecovery = NOW() WHERE idrecovery = :idrecovery", array(":idrecovery"=>$idrecovery
    ));

    }

    public function setPassword($password)
    {

      $sql = new Sql();

      $sql->query("UPDATE tb_users SET despassword = :password WHERE iduser = :iduser", array(
          ":password"=>$password,
          "iduser"=>$this->getiduser()
      ));

    }

}

?>