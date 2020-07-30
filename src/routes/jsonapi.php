<?php
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Psr\Http\Message\ResponseInterface as Response;

    require '../vendor/autoload.php';
   
    function message ($code,$status,$message,$type=null,$object=null) {
        if ($object == null) {
            return array("code" => $code, "status" => $status, "message" => $message);
        } else {
            return array("code" => $code, "status" => $status, "message" => $message, $type => $object);
        }        
    }

    $app = new \Slim\App;
    /**
     * route - CREATE - add new neighbour - POST method
     */
    $app->post
    (
        '/api/voisin', 
        function (Request $request, Response $old_response) {
            try {
                $params = $request->getQueryParams();                
                $nom = $params['nom'];
                $prenom = $params['telephone'];
                $moyenne = $params['adresse'];
                $moyenne = $params['commentaire'];

                $sql = "insert into t_voisins (nom,telephone,adresse,commentaire) values (:nom,:telephone,:adresse,:commentaire)";

                $db_access = new DBAccess ();
                $db_connection = $db_access->getConnection();

                $statement = $db_connection->prepare($sql);                
                $statement->bindParam(':nom', $nom);
                $statement->bindParam(':telephone', $telephone);
                $statement->bindParam(':adresse', $adresse);
                $statement->bindParam(':commentaire', $commentaire);
                $statement->execute();
                
                $response = $old_response->withHeader('Content-type', 'application/json');
                $body = $response->getBody();
                $body->write(json_encode(message(200, 'OK', "The student has been created successfully.")));
            } catch (Exception $exception) {
                
                $response = $old_response->withHeader('Content-type', 'application/json');
                $body = $response->getBody();
                $body->write(json_encode(message(500, 'KO', $exception->getMessage())));
            }

            return $response;
        }
    );

    /**
     * route - READ - get student by id - GET method
     */
    $app->get
    (
        '/api/voisin/{id}', 
        function (Request $request, Response $old_response) {
            try {
                $id = $request->getAttribute('id');                

                $sql = "select * from t_voisins where id = :id";

                $db_access = new DBAccess ();
                $db_connection = $db_access->getConnection();

                $response = $old_response->withHeader('Content-type', 'application/json');
                $body = $response->getBody();

                $statement = $db_connection->prepare($sql);
                $statement->execute(array(':id' => $id));
                if ($statement->rowCount()) {
                    $etudiant = $statement->fetch(PDO::FETCH_OBJ);                    
                    $body->write(json_encode(message(200, 'OK', "Process Successed.", "etudiant", $etudiant)));
                }
                else
                {
                    $body->write(json_encode(message(513, 'KO', "The student with id = '".$id."' has not been found or has already been deleted.")));
                }

                $db_access->releaseConnection();
            } catch (Exception $exception) {
                $response = $old_response->withHeader('Content-type', 'application/json');
                $body = $response->getBody();
                $body->write(json_encode(message(500, 'KO', $exception->getMessage())));
            }
            
            return $response;
        }
    );

    /**
     * route - READ - get all students - GET method
     */
    $app->get
    (
        '/api/voisins', 
        function (Request $request, Response $old_response) {
            try {
                $sql = "Select * From t_voisins";
                $db_access = new DBAccess ();
                $db_connection = $db_access->getConnection();
    
                $response = $old_response->withHeader('Content-type', 'application/json');
                $body = $response->getBody();

                $statement = $db_connection->query($sql);
                if ($statement->rowCount()) {
                    $etudiants = $statement->fetchAll(PDO::FETCH_OBJ);                    
                    $body->write(json_encode(message(200, 'OK', "Process Successed.", "etudiants", $etudiants)));
                } else {
                    $body->write(json_encode(message(512, 'KO', "No student has been recorded yet.")));
                }

                $db_access->releaseConnection();
            } catch (Exception $exception) {
                $response = $old_response->withHeader('Content-type', 'application/json');
                $body = $response->getBody();
                $body->write(json_encode(message(500, 'KO', $exception->getMessage())));
            }
    
            return $response;
        }
    );

    /**
     * route - UPDATE - update a student by id - PUT method
     */
    $app->put
    (
        '/api/voisin/{id}', 
        function (Request $request, Response $old_response) {
            try {

                $id = $request->getAttribute('id');

                $params = $request->getQueryParams();
                $nom = $params['nom'];
                $prenom = $params['telephone'];
                $moyenne = $params['adresse'];
                $moyenne = $params['commentaire'];

                $sql = "update t_voisins set nom = :nom, telephone = :telephone, adresse = :adresse, commentaire=commentaire where id = :id";

                $db_access = new DBAccess ();
                $db_connection = $db_access->getConnection();

                $statement->bindParam(':nom', $nom);
                $statement->bindParam(':telephone', $telephone);
                $statement->bindParam(':adresse', $adresse);
                $statement->bindParam(':commentaire', $commentaire);
                $statement->bindParam(':id', $id);
                $statement->execute();

                $db_access->releaseConnection();

                $response = $old_response->withHeader('Content-Type', 'application/json');
                $body = $response->getBody();
                $body->write(json_encode(message(200, 'OK', "The student has been updated successfully.")));
            } catch (Exception $exception) {
                $response = $old_response->withHeader('Content-Type', 'application/json');
                $body = $response->getBody();
                $body->write(json_encode(message(500, 'KO', $exception->getMessage())));
            }

            return $response;
        }
    );

    /**
     * route - DELETE - delete a student by id - DELETE method
     */
    $app->delete
    (
        '/api/voisin/{id}', 
        function (Request $request, Response $old_response) {
            try {
                $id = $request->getAttribute('id');

                $sql = "delete from t_voivins where id = :id";

                $db_access = new DBAccess ();
                $db_connection = $db_access->getConnection();

                $response = $old_response->withHeader('Content-type', 'application/json');
                $body = $response->getBody();

                $statement = $db_connection->prepare($sql);
                $statement->execute(array(':id' => $id));

                $body->write(json_encode(message(200, 'OK', "The student has been deleted successfully.")));
                $db_access->releaseConnection();
            } catch (Exception $exception) {
                $response = $old_response->withHeader('Content-type', 'application/json');
                $body = $response->getBody();
                $body->write(json_encode(message(500, 'KO', $exception->getMessage())));
            }

            return $response;
        }
    );

    $app->run();
?>