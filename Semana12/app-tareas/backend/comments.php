<?php
require('db.php');

function createComment($taskId, $comment)
{
    global $pdo;
    try {
        $sql = "insert into comments (task_id, comment) value (:task_id, :comment)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'task_id' => $taskId,
            'comment' => $comment
        ]);
        return $pdo->lastInsertId();

    } catch (Exception $e) {
        echo $e->getMessage();
        return 0;
    }
}

function getCommentByTask($taskId)
{
    try {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM comments where task_id = :task_id");
        $stmt->execute(['task_id' => $taskId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $ex) {
        echo "Error al obtener el comentario" . $ex->getMessage();
        return [];
    }
}

function editComment($id, $comment)
{
    global $pdo;
    try {
        $sql = "update comments set comment = :comment where id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'comment' => $comment,
            'id' => $id
        ]);
        //si la cantidad de filas editadas es mayor a cero, retorne true, sino, retorne false;

        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        echo $e->getMessage();
        return false;
    }
}

function deleteComment($id)
{
    global $pdo;
    try {
        $sql = "delete from comments where id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["id" => $id]);
        //si la cantidad de filas editadas es mayor a cero, retorne true, sino, retorne false;
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        echo $e->getMessage();
        return false;
    }
}

function validateInput($input)
{
    if (isset($input['comment'])) {
        return true;
    }
    return false;
}


$method = $_SERVER['REQUEST_METHOD'];
header('Content-Type: application/json');

function getJsonInput()
{
    return json_decode(file_get_contents("php://input"), associative: true);
}

session_start();

if (isset($_SESSION["task_id"])) {
    try {


        //continuar con lo demas
        $taskId = $_SESSION["task_id"];
        switch ($method) {
            case 'GET':
                $comentarios = getCommentByTask($taskId);
                echo json_encode($comentarios);
                break;
            case 'POST':
                //convertir de JSON a array asociativo para facil manipulacion dentro de php
                $input = getJsonInput();
                if (validateInput($input)) {
                    $idTask = createComment($taskId, $input['comment']);
                    if ($idTask > 0) {
                        http_response_code(201);
                        echo json_encode(["message" => "Comentario creado exitosamente. Id:" . $idTask]);
                    } else {
                        http_response_code(500);
                        echo json_encode(['error' => "Error general creando el comentario"]);
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(["error" => "Datos insuficientes"]);
                }
                break;
            case 'PUT':
                $input = getJsonInput();
                if (validateInput($input)) {
                    if (editComment($_GET['id'], $input['comment'])) {
                        http_response_code(201);
                        echo json_encode(['message' => "Comentario actualizada exitosamente"]);
                    } else {
                        http_response_code(500);
                        echo json_encode(['error' => "Error al actualizar el comentario"]);
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'Datos insuficientes']);
                }

                break;
            case 'DELETE':
                if ($_GET['id']) {
                    if (deleteComment($_GET['id'])) {
                        http_response_code(200);
                        echo json_encode(['message' => "Comentario eliminado exitosamente"]);
                    } else {
                        http_response_code(500);
                        echo json_encode(['error' => "Error al eliminar el comentario"]);
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => "Peticion invalida"]);
                }
                break;

            default:
                http_response_code(405);
                echo json_encode(["error" => "Metodo no permitido"]);
        }
    } catch (Exception $exp) {
        http_response_code(500);
        echo json_encode(['error' => "Error al procesar el request"]);
    }
} else {
    http_response_code(401);
    echo json_encode(["error" => "Sesion no activa"]);
}
