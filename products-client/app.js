window.onload = () => {
  const productAddForm = document.querySelector("#productAddForm");

  productAddForm.addEventListener("submit", (e) => {
    e.preventDefault();
    const form = document.forms["productAddForm"];
    const name = form.elements["name"].value;
    const baseprice = form.elements["baseprice"].value;
    const description = form.elements["description"].value;

    const newProduct = {
      name,
      baseprice,
      description,
    };

    fetch("http://localhost:8000/insert", {
      method: "POST",
      body: JSON.stringify(newProduct),
    })
      .then((res) => {
        const errorDiv = document.getElementById("errors");
        const successDiv = document.getElementById("success");
        errorDiv.innerText = "";
        successDiv.innerText = "";

        if (res.status >= 500) {
          errorDiv.innerText = "Une erreur est survenue";
        } else {
          successDiv.innerText = "Le produit a été enregistré";
        }
      })
      .catch((err) => {
        console.error(err);
      });
  });
};
