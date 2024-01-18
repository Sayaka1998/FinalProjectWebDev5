import { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import httpSrv from "../services/httpSrv";

function BookReg() {
    const nav = useNavigate();
    useEffect(() => { //only admin and staff can see this page
        const userType = sessionStorage.getItem("type");
        const sid = sessionStorage.getItem("sid");
        if ((userType !== "Staff" && userType !== "Admin") || sid == null) {
            alert("Only staff and admin can access this page.");
            nav("/blist");
        }
    }, [nav]);

    const [isbn, setIsbn] = useState("");
    const [bname, setBname] = useState("");
    const [author, setAuthor] = useState("");
    const [category, setCat] = useState("");
    const [status, setStatus] = useState("");

    //send form data to backend
    const submitHandle = (e) => {
        e.preventDefault();
        let data = new FormData(e.target);
        data.append("sid", sessionStorage.getItem("sid"));
        httpSrv.bookregister(data)
            .then((res) => {
                alert(res.data.message); // show an message 
            })
            .catch((e) => {
                console.log(e);
                alert("An error occurred while registering the book.");
            });
    };

    return (
        <>
            <h1 className="text-center fw-bolder">Book Registration Page</h1>
            <div className="container-fluid mt-5">
                <div className="row justify-content-center align-items-center g-2">
                    <div className="col-4">
                        <form onSubmit={submitHandle}>
                            <div className="form-floating mb-3">
                                <input type="text" className="form-control" name="isbn" value={isbn} onChange={(e) => setIsbn(e.target.value)} placeholder="ISBN" />
                                <label htmlFor="isbn">ISBN</label>
                            </div>
                            <div className="form-floating mb-3">
                                <input type="text" className="form-control" name="bname" value={bname} onChange={(e) => setBname(e.target.value)} placeholder="Book Name" />
                                <label htmlFor="bname">Book Name</label>
                            </div>
                            <div className="form-floating mb-3">
                                <input type="text" className="form-control" name="author" value={author} onChange={(e) => setAuthor(e.target.value)} placeholder="Author" />
                                <label htmlFor="author">Author</label>
                            </div>
                            <div className="form-floating mb-3">
                                <input type="text" className="form-control" name="category" value={category} onChange={(e) => setCat(e.target.value)} placeholder="Category" />
                                <label htmlFor="category">Category</label>
                            </div>
                            <div className="mb-3">
                                <select className="form-select form-select-lg" name="status" value={status} onChange={(e) => setStatus(e.target.value)}>
                                    <option value="available">available</option>
                                    <option value="unavailable">unavailable</option>
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
