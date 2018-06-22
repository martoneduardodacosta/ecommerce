<?php 

  // Indica o caminho onde a Classe ira pegar os dados
  namespace Hcode\Model;  

  use Rain\Tpl;
  use \Hcode\DB\Sql;
  use \Hcode\Mailer;
  use \Hcode\Model;

  Class Category extends Model 
  {
 
    public static function listAll() 
    {

      $sql = new Sql();

      // b = ponteiro para o arquivo tb_persons
      // a = ponteiro para o arquivo tb_users
      return $sql->select("SELECT * FROM tb_categories ORDER BY descategory");
    
    }

    public function save()
    {

        $sql = new Sql();

        $results = $sql->select("CALL sp_categories_save(:idcategory, :descategory)", array(":idcategory"=>$this->getidcategory(),
          ":descategory"=>$this->getdescategory()
        ));

        var_dump($results);

        $this->setData($results[0]);
    }

    public function get($idcategory)
    {

      $sql = new Sql();

      $results = $sql->select("SELECT * FROM tb_categories WHERE idcategory = :idcategory", [':idcategory'=>$idcategory
       ]);

      $this->setData($results[0]);

    }

    public function delete()
    {

      $sql = new Sql();

      $sql->query("DELETE FROM tb_categories WHERE idcategory = :idcategory", [':idcategory'=>$this->getidcategory()
       ]);

    }
  
}

?>