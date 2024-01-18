import { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import regservice from "../services/regservice";

function Register() {
  const [user, setUser] = useState({
    fname: "",
    lname: "",
    email: "",
    pass: ""
  });
  const [type, setType] = useState("");
  const typeSelectHandler = (e) => {
    setType(e.target.value);
  };
  const nav = useNavigate();

  const chHandler = (val, key) => {
    setUser((preVal) => {
      return { ...preVal, [key]: val };
    });
  };
  const submitHandle = (e) => {
    e.preventDefault();
    let regData = new FormData(e.target);
    regservice
      .register(regData)
      .then((res) => {
        console.log(res);
      })
      .catch((e) => {
        console.log(e);
      });
    alert("Registered Successfully");
    nav("/");
  };

  return (
    <>
      <h1 className="text-center fw-bolder">Register Page</h1>
      <div className="container-fluid mt-5">
        <div className="row justify-content-center align-items-center g-2">
          <div className="col-4">
            <form onSubmit={submitHandle}>
              <div className="mb-3">
                <select className="form-select form-select-lg" name="type" value={type} onChange={typeSelectHandler}>
                  <option value="Customer">Customer</option>
                  <option value="Staff">Staff</option>
                  <option value="Admin">Admin</option>
                </select>
              </div>
              <div className="form-floating mb-3">
                <input type="text" className="form-control" name="fname" value={user.fname} onChange={(e) => chHandler(e.target.value, "fname")} placeholder="First Name" />
                <label htmlFor="fname">First Name</label>
              </div>
              <div className="form-floating mb-3">
                <input type="text" className="form-control" name="lname" value={user.lname} onChange={(e) => chHandler(e.target.value, "lname")} placeholder="Last Name" />
                <label htmlFor="lname">Last Name</label>
              </div>
              <div className="form-floating mb-3">
                <input type="text" className="form-control" name="email" value={user.email} onChange={(e) => chHandler(e.target.value, "email")} placeholder="Email" />
                <label htmlFor="email">Email</label>
              </div>
              <div className="form-floating mb-3">
                <input type="password" className="form-control" name="pass" value={user.pass} onChange={(e) => chHandler(e.target.value, "pass")} placeholder="Password" />
                <label htmlFor="pass">Password</label>
              </div>
              <button type="submit" className="btn btn-outline-primary">
                Register
              </button>
            </form>
          </div>
        </div>
      </div>
    </>
  );
}

export default Register;
