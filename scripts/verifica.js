async function verificaLogin(){
    try {
        const response = await fetch("./phpServer/controladorRestrito.php?acao=exitWhenNotLoggedIn");    // executa uma requisição POST pra login.php com os dados do form
        if (!response.ok) throw new Error(response.statusText); // lança erro caso a resposta do server não seja ok (200).
        const data = await response.json();
        console.log(data);
        if(!data.sucesso){
          window.location = "./login.html"
        }
      }
      catch (e) {  // para caso ocorra qualquer erro na hr de fazer login
        console.error(e);
      }
}

window.addEventListener("load", () => {verificaLogin()})