<!DOCTYPE html>
<html lang="pt-br">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">

     <link rel="shortcut icon" href="../static/img/logos/shortcut.svg" type="image/x-icon">

     <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/uicons-solid-rounded/css/uicons-solid-rounded.css'>

     <link rel="stylesheet" href="../static/css/reset.css">
     <link rel="stylesheet" href="./main.css">
     <link rel="stylesheet" href="../templates/work/css/work.css">

     <!-- Icones -->
     <link rel='stylesheet'
          href='https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css'>
     <link rel="stylesheet" href="../node_modules/bootstrap-icons/font/bootstrap-icons.css">
     
     <!-- <script src="../templates/home/js/google.js"></script> -->
     <?php 

          require_once 'dbConfig.php';

          $users_group = "SELECT DISTINCT us.first_name FROM kantask.groups_users g
          LEFT JOIN kantask.groups as gp ON gp.cod_group = g.cod_group
          LEFT JOIN kantask.users as us ON us.oauth_uid = g.oauth_uid";
          
          $users_query = $db->query($users_group);
          
          $users_data = array(); // Inicializar um array para armazenar todos os usuários
          
          while ($row = $users_query->fetch_assoc()) {
              $users_data[] = $row['first_name'];
          }
     
          $users_json = json_encode(array('users_name' => $users_data));
     ?>

     <script>

        var usersName = <?php echo $users_json; ?>;
        
        localStorage.setItem('usersGroup', JSON.stringify(usersName));
    </script>


     <script src="https://accounts.google.com/gsi/client" async></script>
     
     <title>KanTask - Work</title>
</head>

<body>

     <header class="d-flex flex-wrap justify-content-around py-3 mb-4 border-bottom mx-5">
          <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-dark text-decoration-none">
               <img src="../static/img/logos/logo_dark.svg" width="100" height="32">
          </a>

          <ul class="nav nav-pills">
               <li class="nav-item px-3">
                    <button 
                    class="nav-link d-flex align-items-center p-0" 
                    type="button"
                    data-bs-toggle="modal" data-bs-target="#exampleModal">
                         
                         <i class="fi fi-rr-users-alt p-2" style="font-size: 20px;"></i>
                         <span style="color: #000;">Pessoas</span>
                    </button>
               </li>

          </ul>
     </header>


<!-- MODAL DE USUARIOS DO GRUPO -->

<div class="modal" tabindex="-1" id="exampleModal">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Pessoas do Grupo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?php
         $users_group = "SELECT DISTINCT us.first_name FROM kantask.groups_users g
         LEFT JOIN kantask.groups as gp ON gp.cod_group = g.cod_group
         LEFT JOIN kantask.users as us ON us.oauth_uid = g.oauth_uid";
         
         $users_query = $db->query($users_group);
         
         echo '<ul class="list-group">';
         
         while ($row = $users_query->fetch_assoc()) {
             echo '<li class="list-group-item">' . $row['first_name'] . '</li>';
         }
         
         echo '</ul>';          
        ?>
     </div>
    </div>
  </div>
</div>

<!-- MODAL DE USUARIOS DO GRUPO -->





     <div class="d-flex align-items-center justify-content-start px-5 mt-5">
          <div class=""
               style="border-radius: 1000px; height: 100px; width: 100px; background-position: top; background-size: cover; background-repeat: no-repeat;"
               id="userPhoto">
          </div>
          <div class="px-4">
               <p class="fs-4 fw-light mb-1"><span class="fw-semibold" style="color: var(--bs-primary);" id="userName">
                    </span>, hora de trabalhar!</p>
                    <!-- signOut -->
               <div class="d-flex align-items-center" href="#" 
               style="width: fit-content; cursor: pointer;; color: var(--bs-gray-600);"
               onclick="signOut()"> 
                    <i class="bi bi-box-arrow-right pe-1"></i>
                    <p class="m-0">Sair</p>
               </div>

          </div>
     </div>


     <div class="row mw-100">
          <div onclick="openModal(1)" class="card ms-5 mt-5 card-stl justify-content-center"
               style="cursor: pointer; background-color: var(--bs-gray-100); border-color: var(--bs-green);">
               <div class="text-decoration-none text-dark">
                    <div class="card-body">
                         <i class="bi bi-plus-lg " style="font-size: 25px; color: var(--bs-green); "></i>
                         <p class="fw-semibold py-3">Criar Card</p>
                    </div>
               </div>
          </div>


          <div class="card card-stl ms-3 mt-5 justify-content-center" onclick="viewGroup()" id="inviteCard"
               style="cursor: pointer; background-color: var(--bs-gray-100); border-color: var(--bs-gray-300);">
               <div class="text-decoration-none text-dark">
                    <div class="card-body">
                         <i class="bi bi-person-plus" style="font-size: 25px; color: var(--bs-gray-500); "></i>
                         <p class="fw-semibold py-3">Convidar Membros</p>
                    </div>
               </div>
          </div>

          <div class="card card-stl ms-3 mt-5 justify-content-center" id="groupsCard"
               style="visibility: hidden; cursor: pointer; background-color: var(--bs-gray-100); border-color: var(--bs-gray-300);">
               <div class="text-decoration-none text-dark">
                    <div class="card-body" id="subsCard">

                    </div>
               </div>
          </div>

          <!--  MESSAGES POPUPS -->

          <div class="toast-container position-fixed bottom-0 end-0 p-3">
               <div class="toast copy-toast align-items-center text-bg-primary border-0" style="transition: 0.3s;">
                    <div class="d-flex">
                         <div class="toast-body" id="copy-message" style="color: var(--bs-white);">

                         </div>
                         <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                              aria-label="Close"></button>
                    </div>
               </div>
          </div>

          <div class="toast-container position-fixed bottom-0 end-0 p-3">
               <div class="toast del-toast" style="transition: 0.3s;">
                    <div class="toast-header">
                         <strong class="me-auto" style="color: var(--bs-red);">Atenção!</strong>
                         <button type="button" data-bs-dismiss="toast" aria-label="Close" class="btn-close"></button>
                    </div>
                    <div class="toast-body" id="del-message">

                    </div>
               </div>
          </div>

          <div class="toast-container position-fixed bottom-0 end-0 p-3">
               <div class="toast create-toast" style="transition: 0.3s;">
                    <div class="toast-header">
                         <strong class="me-auto" style="color: var(--bs-green);">Boa!</strong>
                         <button type="button" data-bs-dismiss="toast" aria-label="Close" class="btn-close"></button>
                    </div>
                    <div class="toast-body" id="create-message">

                    </div>
               </div>
          </div>

          <div class="toast-container position-fixed bottom-0 end-0 p-3">
               <div class="toast edit-toast" style="transition: 0.3s;">
                    <div class="toast-header">
                         <strong class="me-auto" style="color: var(--bs-primary);">Opa!</strong>
                         <button type="button" data-bs-dismiss="toast" aria-label="Close" class="btn-close"></button>
                    </div>
                    <div class="toast-body" id="edit-message">

                    </div>
               </div>
          </div>

          <!--  MESSAGES POPUPS -->

     </div>
     </div>

     <div id="modal">
          <div class="box">
               <div class="head">
                    <div>
                         <i id="i-creationMode" class="bi bi-journal-plus pe-1" style="color: var(--bs-primary);"></i>
                         <span id="creationMode">Novo Card</span>

                         <i id="i-editionMode" class="bi bi-pencil-square pe-1" style="color: var(--bs-primary);"></i>
                         <span id="editionMode">Editar Card</span>
                    </div>
                    <button onclick="closeModal()"><i class="bi bi-x-lg"></i></button>
               </div>

               <div class="form">
                    <input type="hidden" id="idInput">

                    <div class="form-group">
                         <label for="creator">Criador</label>
                         <input type="text" id="creator" disabled>
                    </div>

                    <div class="form-group">
                         <label for="task">Nome da tarefa</label>
                         <input type="text" id="task" required>
                    </div>

                    <div class="form-group">
                         <label for="priority">Prioridade</label>
                         <select name="" id="priority">
                              <option>Baixa</option>
                              <option>Média</option>
                              <option>Alta</option>
                         </select>
                    </div>

                    <div class="form-group" style="position: absolute; visibility: hidden;">
                         <label for="column">Coluna</label>
                         <select id="column">
                              <option value="1">A Fazer</option>
                              <option value="2">Em Desenvolvimento</option>
                              <option value="3">Finalizado</option>
                         </select>
                    </div>

                    <div class="form-group">
                         <label for="task">Vencimento</label>
                         <input type="date" id="deadline" required>
                    </div>

                    <button id="btnCreation" onclick="createTask()" class="btn btn-primary w-100"
                         style="color: white;">Criar Card</button>

                    <div class="row justify-content-between">
                         <div class="col-6">
                              <button id="btnEditon" onclick="updateTask()" class="btn btn-success btn-block w-100"
                                   style="color: white;">Salvar</button>
                         </div>
                         <div class="col-6">
                              <button id="btnDelete" onclick="deleteTask()" class="btn btn-danger btn-block w-100"
                                   style="color: white;">Excluir</button>
                         </div>
                    </div>


               </div>

          </div>
     </div>

     <div class="row mx-4 align-items-center justify-content-center">
          <div class="rounded shadow-sm mt-5 d-flex row align-items-center justify-content-between"
               style="overflow: hidden; border: 1px solid var(--bs-gray-200); background-color: var(--bs-gray-100); height: 50vh;">


               <div style="width: 32%; height: 90%; padding: 0;" id="todoColumn" ondrop="drop_handler(event);"
                    ondragover="dragover_handler(event);">
                    <div class="mb-3 rounded shadow-sm d-flex align-items-center justify-content-center"
                         style="background-color: var(--bs-white); border-top: 3px solid var(--bs-orange); color: var(--bs-orange);">
                         <i class="bi bi-archive pe-2 pt-2 pb-2"></i>
                         <h6 style="margin: 0;" class="pb-2  pt-2">Backlog</h6>
                    </div>
                    <div class="bodyColumn w-100 rounded shadow-sm"
                         style="overflow: auto; height: 86%; background-color:var(--bs-white);" data-column="1">

                    </div>
                    <div onclick="openModal(1)" class="mw-100 d-flex align-items-center justify-content-center"
                         style="margin-top: -10px;">
                         <div class="d-flex p-1 rounded justify-content-center"
                              style="cursor: pointer; background-color: var(--bs-orange); width: 15%;">
                              <i class="bi bi-plus-circle" style="color: var(--bs-white);"></i>
                         </div>
                    </div>
               </div>

               <div style="width: 32%; height: 90%; padding: 0;" ondrop="drop_handler(event);"
                    ondragover="dragover_handler(event);">
                    <div class="mb-3 rounded shadow-sm d-flex align-items-center justify-content-center"
                         style="background-color: var(--bs-white); border-top: 3px solid var(--bs-gray-600); color: var(--bs-gray-600);">
                         <i class="bi bi-gear pe-2 pt-2 pb-2"></i>
                         <h6 style="margin: 0;" class="pb-2  pt-2">Em Desenvolvimento</h6>
                    </div>
                    <div class="bodyColumn w-100 rounded shadow-sm"
                         style="overflow: auto; height: 86%; background-color:var(--bs-white);" data-column="2">

                    </div>

                    <div class="mw-100 d-flex align-items-center justify-content-center" style="margin-top: -10px;">
                         <div onclick="openModal(2)" class="d-flex p-1 rounded justify-content-center"
                              style="cursor: pointer; background-color: var(--bs-gray-600); width: 15%;">
                              <i class="bi bi-plus-circle" style="color: var(--bs-white);"></i>
                         </div>
                    </div>

               </div>

               <div style="width: 32%; height: 90%; padding: 0;" ondrop="drop_handler(event);"
                    ondragover="dragover_handler(event);">
                    <div class="mb-3 rounded shadow-sm d-flex align-items-center justify-content-center"
                         style="background-color: var(--bs-white); border-top: 3px solid var(--bs-green); color: var(--bs-green);">
                         <i class="bi bi-flag pe-2 pt-2 pb-2"></i>
                         <h6 style="margin: 0;" class="pb-2  pt-2">Finalizado</h6>
                    </div>
                    <div class="bodyColumn w-100 rounded shadow-sm"
                         style="overflow: auto; height: 86%; background-color:var(--bs-white);" data-column="3">

                    </div>

                    <div class="mw-100 d-flex align-items-center justify-content-center" style="margin-top: -10px;">
                         <div onclick="openModal(3)" class="d-flex p-1 rounded justify-content-center"
                              style="cursor: pointer; background-color: var(--bs-green); width: 15%;">
                              <i class="bi bi-plus-circle" style="color: var(--bs-white);"></i>
                         </div>
                    </div>
               </div>


          </div>
     </div>

     <script src="../static/js/moment.js"></script>
     <script src="../templates/work/js/main.js" defer></script>
     <script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js" defer></script>
</body>

</html>