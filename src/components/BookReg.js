import { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import Bregsrv from "../services/bregsrv";

function BookReg() {
  const nav = useNavigate();
  useEffect(() => {
    const userType = sessionStorage.getItem("type");
    const uid = sessionStorage.getItem("sid");
    if ((userType !== "Staff" && userType !== "Admin") || uid == null) {
      alert("Only staff and admin can access this page.");
      nav("/blist");
    }
  }, [nav]);

  const [regbook, setRegbook] = useState({
    isbn: "",
    bname: "",
    author: "",
    category: ""
  });

  const [status, setStatus] = useState("");
  const statusHandler = (e) => {
    setStatus(e.target.value);
  };

  const chHandler = (val, key) => {
    setRegbook((prevVal) => {
      return { ...prevVal, [key]: val };
    });
  };

  const submitHandle = (e) => {
    e.preventDefault();

    const bregData = {
      isbn: regbook.isbn,
      bname: regbook.bname,
      author: regbook.author,
      category: regbook.category,
      status: status
    };

    // JSON 데이터를 서버로 전송
    Bregsrv.bookregister(JSON.stringify(bregData), {
      headers: {
        "Content-Type": "application/json"
      }
    })
      .then((res) => {
        console.log(res);
        alert(res.data.message); // 서버로부터 받은 메시지를 사용자에게 표시
      })
      .catch((e) => {
        console.log(e);
        alert("An error occurred while registering the book.");
      });
    alert("Book Registered Successfully");
  };

  return (
    <>
      <h1 className="text-center fw-bolder">Book Registration Page</h1>
      <div className="container-fluid mt-5">
        <div className="row justify-content-center align-items-center g-2">
          <div className="col-4">
            <form onSubmit={submitHandle}>
              <div className="form-floating mb-3">
                <input type="text" className="form-control" name="isbn" value={regbook.isbn} onChange={(e) => chHandler(e.target.value, "isbn")} placeholder="ISBN" />
                <label htmlFor="isbn">ISBN</label>
              </div>
              <div className="form-floating mb-3">
                <input type="text" className="form-control" name="bname" value={regbook.bname} onChange={(e) => chHandler(e.target.value, "bname")} placeholder="Book Name" />
                <label htmlFor="bname">Book Name</label>
              </div>
              <div className="form-floating mb-3">
                <input type="text" className="form-control" name="author" value={regbook.author} onChange={(e) => chHandler(e.target.value, "author")} placeholder="Author" />
                <label htmlFor="author">Author</label>
              </div>
              <div className="form-floating mb-3">
                <input type="text" className="form-control" name="category" value={regbook.category} onChange={(e) => chHandler(e.target.value, "category")} placeholder="Category" />
                <label htmlFor="category">Category</label>
              </div>
              <div className="mb-3">
                <select className="form-select form-select-lg" name="status" value={status} onChange={statusHandler}>
                  <option value="available">Available</option>
                  <option value="unavailable">Unavailable</option>
                </select>
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

export default BookReg;
