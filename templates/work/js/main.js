const $modal = document.getElementById("modal");

const dados = localStorage.getItem("dados");
const dadosValid = JSON.parse(dados);

const $creatorInput = dadosValid.nome;


const $creatorPut = document.getElementById("creator")

const $descriptionInput = document.getElementById("task");
const $priorityInput = document.getElementById("priority");
const $deadlineInput = document.getElementById("deadline");
const $idInput = document.getElementById("idInput");
const $columnInput = document.getElementById('column');

const $editionMode = document.getElementById("editionMode");
const $creationMode = document.getElementById("creationMode");
const $ieditionMode = document.getElementById("i-editionMode");
const $icreationMode = document.getElementById("i-creationMode");

const $btnEditon = document.getElementById("btnEditon");
const $btnCreation = document.getElementById("btnCreation");
const $btnDelete = document.getElementById("btnDelete");

var tasksValidation = localStorage.getItem("tasks");
var taskList = tasksValidation ? JSON.parse(tasksValidation) : [];

generateCards();


function openModal(data_column) {
     $modal.style.display = "flex";

     $creatorPut.value = $creatorInput

     $columnInput.value = data_column;

     $creationMode.style.display = "";
     $icreationMode.style.display = "";
     $btnCreation.style.display = "";

     $editionMode.style.display = "none";
     $ieditionMode.style.display = "none";
     $btnEditon.style.display = "none";
     $btnDelete.style.display = "none";

}

function openModalToEdit(id) {
     $modal.style.display = "flex";

     $editionMode.style.display = "";
     $ieditionMode.style.display = "";
     $btnEditon.style.display = "";
     $btnDelete.style.display = "";

     $creationMode.style.display = "none";
     $icreationMode.style.display = "none";
     $btnCreation.style.display = "none";

     const index = taskList.findIndex(function (task) {
          return task.id == id;
     });

     const task = taskList[index];

     $idInput.value = task.id;
     $descriptionInput.value = task.description;
     $priorityInput.value = task.priority;
     $deadlineInput.value = task.deadline;
     $columnInput.value = task.column;
     $creatorPut.value = task.creator;

}


function signOut() {
     google.accounts.id.disableAutoSelect();
     google.accounts.id.prompt((notification) => {
          if (notification.isNotDisplayed()) {
               window.location.href = 'index.html';
          } else {
               notification.dismiss();
          }
     });
}


function closeModal() {
     $modal.style.display = "none";

     $idInput.value = "";
     $descriptionInput.value = "";
     $deadlineInput.value = "";
     $columnInput.value = "";

}

function resetColumns() {
     document.querySelector('[data-column="1"]').innerHTML = '';
     document.querySelector('[data-column="2"]').innerHTML = '';
     document.querySelector('[data-column="3"]').innerHTML = '';
}

function generateCards() {

     const infos = localStorage.getItem("dados");
     const infosValid = JSON.parse(infos);
     var $userPhoto = document.getElementById('userPhoto');

     $userPhoto.style.backgroundImage = 'url(' + infosValid.img + ')';
     document.getElementById("userName").innerText = infosValid.nome;

     resetColumns();

     taskList.forEach(function (task) {
          const formattedDate = moment(task.deadline).format('DD/MM/YYYY');

          const columnBody = document.querySelector(`[data-column="${task.column}"]`);

          const card = `
               <div
               id="${task.id}" 
               class="task" 
               onclick="openModalToEdit(${task.id})"
               draggable="true" 
               ondragstart="dragstart_handler(event);"
               >
                    <div class="cards" style="cursor:pointer;">
                         <div class="taskinfo">
                              <span class="${task.priority}">${task.priority}</span>
                              <p>${task.description}</p>
                         </div>
                         <div class="taskpro">
                              <div class="taskproDiv">
                                   <p>Vencimento:</p> <span>${formattedDate}</span>
                              </div>

                              <div class="taskproDiv">
                                   <p>Criador:</p> <span>${task.creator}</span>
                              </div>
                         </div>
                    </div>
               </div>
          `;

          columnBody.innerHTML += card;

     });
}

function createTask() {
     const newTask = {
          id: Math.floor(Math.random() * 9999999),
          creator: $creatorInput,
          description: $descriptionInput.value,
          priority: $priorityInput.value,
          deadline: $deadlineInput.value,
          column: $columnInput.value,
     }

     taskList.push(newTask);

     localStorage.setItem("tasks", JSON.stringify(taskList));

     closeModal();
     generateCards();

     const toastContentCreate = document.querySelector(".create-toast");
     const toastCreate = new bootstrap.Toast(toastContentCreate);
     document.getElementById("create-message").innerText = "Card criado com sucesso!";

     toastCreate.show();
}

function updateTask() {
     const task = {
          id: $idInput.value,
          description: $descriptionInput.value,
          priority: $priorityInput.value,
          deadline: $deadlineInput.value,
          column: $columnInput.value,
          creator: $creatorPut.value,
     }

     const index = taskList.findIndex(function (task) {
          return task.id == $idInput.value;
     });

     taskList[index] = task;

     localStorage.setItem("tasks", JSON.stringify(taskList));

     closeModal();
     generateCards();

     const toastContentEdit = document.querySelector(".edit-toast");
     const toastEdit = new bootstrap.Toast(toastContentEdit);
     document.getElementById("edit-message").innerText = "Card editado com sucesso!";

     toastEdit.show();
}


function deleteTask() {

     const idToDelete = $idInput.value;

     const indexToDelete = taskList.findIndex(task => task.id === idToDelete);


     if (indexToDelete !== -1) {
          taskList.splice(indexToDelete, 1);
          localStorage.setItem("tasks", JSON.stringify(taskList));
          closeModal();
          generateCards();
          
          const toastContentDel = document.querySelector(".del-toast");
          const toastDel = new bootstrap.Toast(toastContentDel);
          document.getElementById("del-message").innerText = "Card deletado com sucesso!";

          toastDel.show();
     }
}



function changeColumn(task_id, column_id) {
     if (task_id && column_id) {

          taskList = taskList.map((task) => {
               if (task_id != task.id) return task;

               return {
                    ...task,
                    column: column_id,

               };
          });
     }

     localStorage.setItem("tasks", JSON.stringify(taskList));
     generateCards();
}


function dragstart_handler(ev) {
     ev.dataTransfer.setData("my_custom_data", ev.target.id);
     ev.dropEffect = "move";
}

function dragover_handler(ev) {
     ev.preventDefault();
     ev.dataTransfer.dropEffect = "move";
}
function drop_handler(ev) {
     ev.preventDefault();
     const task_id = ev.dataTransfer.getData("my_custom_data");
     const column_id = ev.target.dataset.column;
     changeColumn(task_id, column_id);
}

// GRUPOS E VISUALIZAÇÃO DELES

function copyToClipboard() {

     var copyText = document.getElementById("copyGroup");

     var tempInput = document.createElement("input");
     tempInput.value = copyText.value;

     document.body.appendChild(tempInput);

     tempInput.select();
     tempInput.setSelectionRange(0, 99999);

     document.execCommand("copy");

     document.body.removeChild(tempInput);

     const toastContentCopy = document.querySelector(".copy-toast");
     const toastCopy = new bootstrap.Toast(toastContentCopy);
     document.getElementById("copy-message").innerText = "Texto copiado com sucesso!";

     toastCopy.show();

}

var groups = JSON.parse(localStorage.getItem("grupo"));
const $inviteCard = document.getElementById("inviteCard");
const $subsCard = document.getElementById("subsCard");
const $groupsCard = document.getElementById("groupsCard");



function viewGroup() {

     $inviteCard.style.display = "none"
     $groupsCard.style.visibility = "visible"

     const groupsDiv = `
               <div class="d-flex mb-3">
                    <input id="copyGroup" value="${groups.cod_gp}" disabled readonly 
                    style="width: 37%; border: none;">
                    <i class="bi bi-copy ps-2" style="color: var(--bs-primary); font-size: 16px;" onclick="copyToClipboard()"></i>
               </div>
               <p class="fw-semibold">${groups.nome_gp}</p>
     `;

     $groupsCard.innerHTML = groupsDiv;
}




// fetch('auth_init.php', {
//      method: 'GET',
//      headers: {
//           'Content-Type': 'application/json',
//      },

// })
//      .then((result) => result)
//      .then((userList) => { console.log(userList.json()) })
//      .catch((error) => {
//           console.error(error);
//      })
