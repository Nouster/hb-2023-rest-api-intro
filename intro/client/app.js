window.onload = async () => {
  try {
    const res = await fetch("http://127.0.0.1:8000");
    const users = await res.json();
    console.log(users);
  } catch (err) {
    console.log("Une erreur est survenue");
  }

  // fetch("http://127.0.0.1:8000")
  //   .then((res) => res.json())
  //   .then((users) => console.log(users))
  //   .catch(() => console.log("Une erreur est survenue"));
  // console.log("après le fetch");
};
