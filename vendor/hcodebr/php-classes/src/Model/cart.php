<?php 

  // Indica o caminho onde a Classe ira pegar os dados
  namespace Hcode\Model;  

  use Rain\Tpl;
  use \Hcode\DB\Sql;
  use \Hcode\Mailer;
  use \Hcode\Model;
  use \Hcode\Model\User;
  use \Hcode\Model\Product;
    

  Class Cart extends Model 
  {
 

      const SESSION =   "Cart";


public static function getFromSession()
  {
    $cart = new Cart();
  
      if (isset($_SESSION[Cart::SESSION]) && (int)$_SESSION[Cart::SESSION]['idcart'] > 0) {
        $cart->get((int)$_SESSION[Cart::SESSION]['idcart']);
  
    } else {
      
        $cart->getFromSessionID();
        
        if (!(int)$cart->getidcart() > 0) {
          $data = [
          'dessessionid'=>session_id()
        ];

        if (User::checkLogin(false)) {
    
          $user = User::getFromSession();
          
          $data['iduser'] = $user->getiduser(); 
        }
        
        $cart->setData($data);

        $cart->save();

        $cart->setToSession();
      
      }
    
    }
  
    return $cart;
  }
  

  public function setToSession()
  
  {
  
    $_SESSION[Cart::SESSION] = $this->getValues();
  
  }
  
 
  public function getFromSessionID()
  {
  
    $sql = new Sql();
  
    $results = $sql->select("SELECT * FROM tb_carts WHERE dessessionid = :dessessionid", [
      ':dessessionid'=>session_id()
    ]);
   
    if (count($results) > 0) {
      $this->setData($results[0]);
    }
  
  }


  public function get(int $idcart)
  {

    $sql = new Sql();
    
    $results = $sql->select("SELECT * FROM tb_Carts WHERE idcart = :idcart", [
        ':idcart'=>$idcart
        ]); 

    if (count($results) > 0) {

       $this->setData($results[0]);

    }   

  }


  public function save()
  {
    
    $sql = new Sql();
    
    $results = $sql->select("CALL sp_carts_save(:idcart, :dessessionid, :iduser, :deszipcode, :vlfreight, :nrdays)", [
      ':idcart'=>$this->getidcart(),
      ':dessessionid'=>$this->getdessessionid(),
      ':iduser'=>$this->getiduser(),
      ':deszipcode'=>$this->getdeszipcode(),
      ':vlfreight'=>$this->getvlfreight(),
      ':nrdays'=>$this->getnrdays()
    ]);

    $this->setData($results[0]);

  }

    
}

?>