<?php
class UserController{
    public function __construct(private UsersGateway $gateway) {
    }
    public function processAuthentication($method) {
        if($method =="POST"){
            $data = (array) json_decode(file_get_contents("php://input"), true);
            $errors = $this->getValidationErrors($data);
            if(!empty($errors)){
                http_response_code(422);
                echo json_encode(["message"=>"username &(or) password cannot be empty"]);
                return;
            }
            $validUser = $this->gateway->getvaliduser($data);
            if(empty($validUser)){
                http_response_code(401);
                echo json_encode(["message"=>"Invalid username and /or password"]);
                return;
            }
            echo json_encode($validUser);
        }
        else{
            http_response_code(404);
            header("Allow: POST");
        }
       
    }
    public function processRequest(string $method, ?string $id): void
    {
        if ($id) {
            
            $this->processResourceRequest($method, $id);
            
        } else {
            $this->processCollectionRequest($method);
        }
    }
    private function processResourceRequest(string $method, string $id){
        $user = $this->gateway->get($id);
                if(empty($user)||$user == false){
                    http_response_code(404);
                    echo json_encode(["message"=>"user not found"]);
                    return;
                }
        switch($method){
            case "GET":
                echo json_encode($user);
                break;
            case "DELETE":
                $rows = $this->gateway->delete($id);
                echo json_encode([
                    "message" => "Product $id deleted",
                    "rows" => $rows
                ]);
                break;
            default:
            http_response_code(500);
            header("Allow: GET, PATCH, DELETE");
            break;
            
        }
    }

    private function processCollectionRequest(string $method){
        switch($method){
            case "GET":
                echo json_encode($this->gateway->getAll());
                break;
            case "POST":
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