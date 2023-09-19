<?php
class UserController{
    public function __construct(private UsersGateway $gateway) {
    }
    public function processRequest(string $method, ?string $id): void
    {
        if ($id) {
            
            $this->processResourceRequest($method, $id);
            
        } else {
            echo " : Multi : ";
            $this->processCollectionRequest($method);
        }
    }
    private function processResourceRequest(string $method, string $id){
        switch($method){
            case "GET":
                echo "In get";
                echo json_encode($this->gateway->getAll());
                break;
            case "DELETE":
                $rows = $this->gateway->delete($id);
                echo json_encode([
                    "message" => "Product $id deleted",
                    "rows" => $rows
                ]);
                break;
            default:
            //echo "In default";
            http_response_code(500);
            header("Allow: GET, PATCH, DELETE");
            break;
            
        }
    }

    private function processCollectionRequest(string $method){
        var_dump($method);
        echo "asjhdlak sjkdfha :";
        if($method == "GET"){
            echo " : It is GET : ";
        }
        switch($method){
            case "GET":
                echo ":: In the get::";
                echo json_encode($this->gateway->getAll());
                break;
            case "POST":
                echo ": In the post :";break;
                $data = (array) json_decode(file_get_contents("php://input"), true);
                $errors = $this->getValidationErrors($data);
                
                if ( ! empty($errors)) {
                    http_response_code(422);
                    echo json_encode(["errors" => $errors]);
                    break;
                }
                $id = $this->gateway->create($data);
                
                http_response_code(201);
                echo json_encode([
                    "message" => "User created",
                    "id" => $id
                ]);
                break;
            default:
            echo "It is coming to default";
            break;
            http_response_code(405);
            header("Allow: GET, POST");
        }
        echo " END of switch ";
    }
    private function getValidationErrors(array $data, bool $is_new = true): array
    {
        $errors = [];
        
        if ($is_new && empty($data["name"])) {
            $errors[] = "name is required";
        }
        if($is_new && empty($data["password"])){
            $errors[] = "password is required";
        }
        
        return $errors;
    }
}
?>