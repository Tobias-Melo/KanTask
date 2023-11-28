<?php
// Load the database configuration file 
require_once 'dbConfig.php';



// Retrieve JSON from POST body 
$jsonStr = file_get_contents('php://input');
$jsonObj = json_decode($jsonStr);

if (!empty($jsonObj->request_type) && $jsonObj->request_type == 'user_auth') {
    $credential = !empty($jsonObj->credential) ? $jsonObj->credential : '';

    // Decode response payload from JWT token
    list($header, $payload, $signature) = explode(".", $credential);
    $responsePayload = json_decode(base64_decode($payload));

    if (!empty($responsePayload)) {
        // The user's profile info
        $oauth_provider = 'google';
        $oauth_uid  = !empty($responsePayload->sub) ? $responsePayload->sub : '';
        $first_name = !empty($responsePayload->given_name) ? $responsePayload->given_name : '';
        $last_name  = !empty($responsePayload->family_name) ? $responsePayload->family_name : '';
        $email      = !empty($responsePayload->email) ? $responsePayload->email : '';
        $picture    = !empty($responsePayload->picture) ? $responsePayload->picture : '';

        // Check whether the user data already exist in the database
        $query = "SELECT * FROM users WHERE oauth_provider = '$oauth_provider' AND oauth_uid = '$oauth_uid'";
        $result = $db->query($query);

        // Inicialize $output com valores padrão
        $output = [
            'status' => 0,
            'msg' => 'Erro desconhecido.',
        ];

        if ($result) {
            if ($result->num_rows > 0) {
                // Update user data if already exists
                $query = "UPDATE users SET first_name = '$first_name', last_name = '$last_name', email = '$email', picture = '$picture', modified = NOW() WHERE oauth_provider = '$oauth_provider' AND oauth_uid = '$oauth_uid'";
                $update = $db->query($query);

                $output['redirect'] = 'workspace.html';
                $output['status'] = 1;
                $output['msg'] = 'Usuário autenticado com sucesso!';
                $output['pdata'] = [
                    'nome' => $first_name,
                    'sobrenome' => $last_name,
                    'email' => $email,
                    'img' => $picture,
                    'id' => $oauth_uid,
                ];
            } else {
                // Insert user data
                $query = "INSERT INTO users VALUES (NULL, '$oauth_provider', '$oauth_uid', '$first_name', '$last_name', '$email', '$picture', NOW(), NOW())";
                $insert = $db->query($query);

                $output['redirect'] = 'login.html';
                $output['status'] = 1;
                $output['msg'] = 'Usuário não encontrado. Redirecionando para a página de login.';
                $output['pdata'] = [
                    'nome' => $first_name,
                    'sobrenome' => $last_name,
                    'email' => $email,
                    'img' => $picture,
                    'id' => $oauth_uid,
                ];
            }
        } else {
            // Handle database error
            $output['msg'] = 'Erro no banco de dados.';
        }
    }
    echo json_encode($output);
}

// Saída final



function generateUniqueCode($length = 6)
{
    $characters = '0123456789';
    $code = '';

    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }

    return $code;
}

// ... Seu código PHP existente ...

if (!empty($jsonObj->request_type) && $jsonObj->request_type == 'create_group') {
    $group_name = !empty($jsonObj->group_name) ? $jsonObj->group_name : '';
    $oauth_uid = !empty($jsonObj->oauth_uid) ? $jsonObj->oauth_uid : '';

    // Verifica se o nome do grupo já existe no banco de dados
    $check_query = "SELECT * FROM groups WHERE name = '$group_name'";
    $check_result = $db->query($check_query);


    if ($check_result->num_rows > 0) {
        // O nome do grupo já existe, retorna uma mensagem de erro
        $output = [
            'status' => 0,
            'msg' => 'O nome do grupo já está em uso. Escolha outro nome.',
        ];
    } else {
        // Gera um código único para o grupo
        $unique_code = generateUniqueCode();

        // Insere dados na tabela de grupos
        $insert_query = "INSERT INTO groups (name, cod_group, creator_id) VALUES ('$group_name', '$unique_code', '$oauth_uid')";
        $insert_result = $db->query($insert_query);

        $insert_query_group = "INSERT INTO groups_users (cod_group, oauth_uid) VALUES ('$unique_code', '$oauth_uid')";
        $insert_result_group = $db->query($insert_query_group);

        $users_group = "SELECT DISTINCT gp.name FROM kantask.groups_users g
        LEFT JOIN kantask.groups as gp ON gp.cod_group = g.cod_group";

        $users_query = $db->query($users_group);


        $users_data = $users_query->fetch_assoc();
        $users_name = $users_data['name'];


        if ($insert_result) {
            $output = [
                'status' => 1,
                'msg' => 'Grupo criado com sucesso!',
                'group_code' => $unique_code,
                'group_name' => $group_name,
                'list_name' => $users_name,
            ];
        } else {
            $output = [
                'status' => 0,
                'msg' => 'Erro ao criar grupo.',
            ];
        }
    }

    echo json_encode($output);
}

// Adicione a lógica para a solicitação 'join_group' aqui
if (!empty($jsonObj->request_type) && $jsonObj->request_type == 'join_group') {
    $group_code = !empty($jsonObj->group_code) ? $jsonObj->group_code : '';
    $oauth_uid = !empty($jsonObj->oauth_uid) ? $jsonObj->oauth_uid : '';

    error_log('Valor do código do grupo: ' . $group_code);
    // Verifica se o código do grupo existe no banco de dados
    $check_query = "SELECT * FROM groups WHERE cod_group = '$group_code'";
    $check_result = $db->query($check_query);

    if ($check_result->num_rows > 0) {
        // O código do grupo existe, adiciona o usuário ao grupo ou realiza outras ações necessárias
        // Aqui você pode implementar a lógica para associar o usuário ao grupo
        // e realizar outras operações necessárias
        // O código do grupo existe, obtenha o nome do grupo
        $group_data = $check_result->fetch_assoc();
        $group_name = $group_data['name'];

        $users_group = "SELECT DISTINCT gp.name FROM kantask.groups_users g
        LEFT JOIN kantask.groups as gp ON gp.cod_group = g.cod_group";

        $users_query = $db->query($users_group);


        $users_data = $users_query->fetch_assoc();
        $users_name = $users_data['name'];


        $insert_query = "INSERT INTO groups_users (cod_group, oauth_uid) VALUES ('$group_code', '$oauth_uid')";
        $insert_result = $db->query($insert_query);

        $output = [
            'status' => 1,
            'msg' => 'Entrou no grupo com sucesso!',
            'group_name' => $group_name,
            'list_name' => $users_name,
            // Adicione o nome do grupo à resposta se necessário
        ];
    } else {
        // O código do grupo não existe, retorna uma mensagem de erro
        $output = [
            'status' => 0,
            'msg' => 'Código do grupo inválido. Verifique o código e tente novamente.',
        ];
        error_log('Erro no servidor: ' . $output['msg']);
    }

    echo json_encode($output);
}


$users_group = "SELECT DISTINCT gp.name FROM kantask.groups_users g
LEFT JOIN kantask.groups as gp ON gp.cod_group = g.cod_group";

$users_query = $db->query($users_group);

$users_data = $users_query->fetch_assoc();
$users_name = $users_data['name'];

// Enviar a variável para o frontend usando JSON
$users_json = json_encode(array('users_name' => $users_name));
