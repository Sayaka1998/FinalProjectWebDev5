function BookRow(props) {
    const btnHandler = () => { // return the selected book data to the book list
        props.borrow(props.book);
    } 
    return(
        <tr>
            <td>{props.book.isbn}</td>
            <td>{props.book.bname}</td>
            <td>{props.book.author}</td>
            <td>{props.book.category}</td>
            <td>
                {
                    sessionStorage.getItem("type") != "Customer" ?
                    (props.book.status):
                    <button type="button" className="btn btn-outline-secondary" disabled={props.book.status == "unavailable"} onClick={btnHandler}>{props.book.status}</button>
                }
            </td>            
        </tr>
    )
}
export default BookRow;