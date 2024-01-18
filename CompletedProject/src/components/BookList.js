import { useNavigate } from "react-router-dom";
import { useState, useEffect } from "react";
import httpSrv from "../services/httpSrv";
import BookRow from "./BookRow";
import CartRow from "./CartRow";
function Blist() {
    const nav = useNavigate();
    useEffect(()=>{
        // if the user doesn't log in, back to the login page
        if(sessionStorage.getItem("sid") == undefined) {
            nav("/");
        }
    });

    const [cart,setCart] = useState([]);
    const addCart = (newBook) => {
        let flag = true;
        for(let book of cart) {
            // check if the book is already added to the user cart
            if(book.isbn == newBook.isbn){
                flag = false;
                return false;
            }
        }
        if(flag) { // if the book data doesn't exist in the cart, add the book to the cart
            setCart(prevCart=>{
                return [...prevCart,newBook];
            })
        }
    } 

    const rmvBook = (rmvIdx) => { // remove the book from the cart
        cart.splice(rmvIdx,1);
        setCart([...cart]);
    }

    const borrow = () => {
        if(cart.length != 0) { // if some books in the cart 
            let data = new FormData();
            data.append("book",JSON.stringify(cart)); // book data array 
            data.append("sid",sessionStorage.getItem("sid"));
            httpSrv.borrow(data).then(
                res=>{
                    if(res.data === "Login first.") {
                        alert(res.data);
                        nav("/");
                    } else {
                        alert(res.data);
                        window.location.reload();
                    }
                },
                rej => {
                    alert(rej);
                }
            )
        }
    }

    const [books, setBooks] = useState([]);
    const bookLoad = () => {
        if (books.length == 0) { // load the book in the library from the back-end
            let data = new FormData();
            data.append("sid",sessionStorage.getItem("sid"));
            httpSrv.blist(data).then(
                res => {
                    if(Array.isArray(res.data)) {
                        setBooks(res.data);
                    } else if(res.data === "Login first.") {
                        alert(res.data);
                        nav("/");
                    } else {
                        alert(res.data);
                    }
                },
                rej => {
                    alert(rej);
                }
            )
        }
    }
    bookLoad();

    return (
        <>
            <div className="container-fluid">
                <div className="row justify-content-center align-items-center g-2">
                    <div className="col">
                        <div className="table-responsive">
                            <table className="table table-success">
                                <thead>
                                    <tr>
                                        <th scope="col">ISBN</th>
                                        <th scope="col">Book Name</th>
                                        <th scope="col">Author</th>
                                        <th scope="col">Category</th>
                                        <th scope="col">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {books.map((book, idx) => { return (<BookRow key={idx} book={book} borrow={addCart}/>) })}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div className="col" style={{display:(sessionStorage.getItem("type") != "Customer") ? "none":"block"}}>
                        <div className="table-responsive">
                            <table className="table table-secondary">
                                <thead>
                                    <tr>
                                        <th>ISBN</th>
                                        <th>Book Name</th>
                                        <th>Author</th>
                                        <th>Category</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {cart.map((book,idx)=>{return(<CartRow key={idx} book={book} rmv={rmvBook}/>)})}
                                </tbody>
                            </table>
                            <button type="button" className="btn btn-outline-success" onClick={borrow}>Borrow</button>
                        </div>
                    </div>
                </div>
            </div>
        </>
    )
}
export default Blist;