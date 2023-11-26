function submitForm(e) {
    e.preventDefault(); // Prevenir o envio padrão do formulário

    const infos = localStorage.getItem("dados");
    const infosValid = JSON.parse(infos);
    const isCreateGroup = document.getElementById('group_name').value !== '';
    const oauth_uid = infosValid.id;

    // Dados a serem enviados para o servidor
    var requestData = {
        request_type: isCreateGroup ? 'create_group' : 'join_group',
        group_name: document.getElementById('group_name').value,
        group_code: document.getElementById(isCreateGroup ? 'created_group_code' : 'join_group_code').value,
        oauth_uid: oauth_uid,
    };



    // Faça uma requisição AJAX para o servidor (você pode usar fetch ou outras bibliotecas)
    // Certifique-se de ajustar a URL conforme necessário
    fetch('auth_init.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(requestData),
    })
        .then(response => {
            console.log('Status da resposta:', response.status);

            // Verifique se a resposta é OK (status 200)
            if (response.ok) {
                return response.text();
            } else {
                throw new Error('Erro na requisição. Status: ' + response.status);
            }
        })
        .then(data => {

            console.log(data)
            try {
                // Tente analisar o JSON
                const jsonData = JSON.parse(data);

                // Manipule a resposta do servidor aqui
                if (jsonData.status === 1) {
                    if (isCreateGroup) {
                        document.getElementById('created_group_code').value = jsonData.group_code;

                        localStorage.setItem("grupo", JSON.stringify({
                            "nome_gp": jsonData.group_name,
                            "cod_gp": jsonData.group_code,
                        }));
                    } else {
                        console.log("chegou aqui")
                    }

                    console.log("chegou aqui");
                    window.location.href = 'workspace.html';
                } else {
                    const toastContent = document.querySelector(".toast");
                    const toast = new bootstrap.Toast(toastContent);
                    document.getElementById("error-message").innerText = jsonData.msg;

                    toast.show();
                }
            } catch (error) {
                // Se ocorrer um erro ao analisar o JSON, imprima no console
                console.error('Erro ao analisar JSON:', error);
            }
        })
        .catch((error) => {
            console.error('Erro na requisição:', error);
        });

    return false; // Evitar que o evento do formulário prossiga com o comportamento padrão
}
