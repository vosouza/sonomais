document.getElementById("loginForm").addEventListener("submit", function (e) {
    e.preventDefault();
  
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();
    const message = document.getElementById("login-message");
  
    if (!email || !password) {
      message.textContent = "Preencha todos os campos.";
      return;
    }
  
    // Simulação de login — futuramente será substituído por PHP
    if (email === "admin@admin.com" && password === "123456") {
      message.style.color = "green";
      message.textContent = "Login bem-sucedido!";
      // Aqui você pode redirecionar ou mostrar a tela de cadastro depois
    } else {
      message.style.color = "red";
      message.textContent = "E-mail ou senha inválidos.";
    }
  });
  