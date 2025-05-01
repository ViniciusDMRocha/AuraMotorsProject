<?php

function exitWhenNotLoggedIn()
{ 
  if (!isset($_SESSION['loggedIn'])) {    // verifica se o loggedIn é true, caso não seja executa o if
    header("Location: ../login.html");   // retorna o usuario para o index.html
    exit();
  }
}

function logout()
{
    session_start();
    // apaga as variáveis de sessão de $_SESSION
    session_unset();
    // destrói a sessão e as variáveis fisicamente (em arquivo)
    session_destroy();
    // exclui o cookie da sessão no computador do usuário
    setcookie(session_name(), "", 1, "/");
    // redireciona o usuário para a página de login
    header('Location: ../index.html');
    exit();
}

?> 