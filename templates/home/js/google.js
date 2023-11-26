
const infos = localStorage.getItem("dados");

document.addEventListener("DOMContentLoaded", function () {
     userInfo();
});

function userInfo() {
     if (window.location.pathname.endsWith("login.html")) {
          const infosValid = JSON.parse(infos);
          const $firstName = document.getElementById("firstname");
          const $lastName = document.getElementById("lastname");
          const $email = document.getElementById("email");

          $firstName.value = infosValid.nome;
          $lastName.value = infosValid.sobrenome;
          $email.value = infosValid.email;
     }
}

function handleCredentialResponse(response) {
     fetch("auth_init.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ request_type: 'user_auth', credential: response.credential }),
     })
          .then(response => {
               if (response.ok) {
                    return response.text();
               } else {
                    throw new Error('Erro na requisição. Status: ' + response.status);
               }
          })
          .then(data => {
               try {
                    const jsonData = JSON.parse(data);

                    console.log(jsonData)

                    if (jsonData.status === 1) {

                         localStorage.setItem("dados", JSON.stringify({
                              "nome": jsonData.pdata.nome,
                              "sobrenome": jsonData.pdata.sobrenome,
                              "email": jsonData.pdata.email,
                              "img": jsonData.pdata.img,
                              "id": jsonData.pdata.id,
                         }));


                         // Redireciona para a página apropriada
                         if (jsonData.redirect) {
                              window.location.href = jsonData.redirect;
                         } else {
                              window.location.href = 'workspace.html';
                         }
                    } else {
                         const errorMessageElement = document.getElementById("error-message");

                         if (errorMessageElement) {
                              errorMessageElement.innerText = jsonData.msg;
                              const toastContent = document.querySelector(".toast");
                              const toast = new bootstrap.Toast(toastContent);
                              toast.show();
                         } else {
                              console.error('Element with ID "error-message" not found.');
                         }
                    }
               } catch (error) {
                    console.error('Erro ao analisar JSON:', error);
               }
          })
          .catch((error) => {
               console.error('Erro na requisição:', error);
          });
}

// Sign out the user
function signOut(authID) {
     document.getElementsByClassName("pro-data")[0].innerHTML = '';
     document.querySelector("#btnWrap").classList.remove("hidden");
     document.querySelector(".pro-data").classList.add("hidden");
}

window.onload = function () {
     if (window.location.pathname.endsWith("index.html") || window.location.pathname.endsWith("")) {
          google.accounts.id.initialize({
               client_id: "405479233539-98fjc42f7e56f2cstknv0411rnb584jp.apps.googleusercontent.com",
               callback: handleCredentialResponse
          });
          google.accounts.id.renderButton(
               document.getElementById("buttonDiv"),
               {
                    theme: "outline",
                    size: "large",
                    type: "standard",
                    shape: "pill",
                    text: "continue_with",
                    logo_alignment: "left"

               } // customization attributes
          );
          google.accounts.id.renderButton(
               document.getElementById("buttonDiv_1"), {
               theme: "outline",
               size: "large",
               type: "standard",
               shape: "pill",
               text: "continue_with"
          });

          google.accounts.id.renderButton(
               document.getElementById("buttonDiv_2"), {
               theme: "outline",
               size: "large",
               type: "standard",
               shape: "pill",
               text: "continue_with"
          });
          google.accounts.id.renderButton(
               document.getElementById("buttonDiv_3"), {
               theme: "outline",
               size: "large",
               type: "standard",
               shape: "pill",
               text: "continue_with"
          });
          google.accounts.id.renderButton(
               document.getElementById("buttonDiv_4"), {
               theme: "outline",
               size: "large",
               type: "standard",
               shape: "pill",
               text: "continue_with"
          });
          google.accounts.id.prompt(); // also display the One Tap dialog
     }
}