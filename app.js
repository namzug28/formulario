const express = require("express");
const mysql = require("mysql2");
const bodyParser = require("body-parser");

const app = express();
const port = 3000;


const pool = mysql.createPool({
  host: "localhost",
  user: "root",
  password: "",
  database: "personas",
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0,
});

app.use(bodyParser.urlencoded({ extended: true }));
app.use(bodyParser.json());


app.get("/usuarios", (req, res) => {
  pool.query("SELECT * FROM usuarios", (err, results) => {
    if (err) {
      return res.status(500).send(err);
    }
    res.json(results);
  });
});


app.get("/usuarios/:id", (req, res) => {
  const id = req.params.id;
  pool.query("SELECT * FROM usuarios WHERE id = ?", [id], (err, results) => {
    if (err) {
      return res.status(500).send(err);
    }
    if (results.length === 0) {
      return res.status(404).send("Usuario no encontrado");
    }
    res.json(results[0]);
  });
});


app.post("/usuarios", (req, res) => {
  const { nombre, correo } = req.body;
  pool.query(
    "INSERT INTO usuarios (nombre, correo) VALUES (?, ?)",
    [nombre, correo],
    (err, result) => {
      if (err) {
        return res.status(500).send(err);
      }
      res.send("Usuario creado con éxito");
    }
  );
});


app.put("/usuarios/:id", (req, res) => {
  const id = req.params.id;
  const { nombre, correo } = req.body;
  pool.query(
    "UPDATE usuarios SET nombre = ?, correo = ? WHERE id = ?",
    [nombre, correo, id],
    (err, result) => {
      if (err) {
        return res.status(500).send(err);
      }
      res.send("Usuario actualizado con éxito");
    }
  );
});


app.delete("/usuarios/:id", (req, res) => {
  const id = req.params.id;
  pool.query("DELETE FROM usuarios WHERE id = ?", [id], (err, result) => {
    if (err) {
      return res.status(500).send(err);
    }
    res.send("Usuario eliminado con éxito");
  });
});

app.listen(port, () => {
  console.log(`Servidor escuchando en el puerto ${port}`);
});
