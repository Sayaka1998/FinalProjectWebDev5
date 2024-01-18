function BookRow(props) {
    const btnHandler = () => {
        props.borrow(props.book);
    } 
    return(
        <tr>
            <td>{props.book.isbn}</td>
            <td>{props.book.bname}</td>
            <td>{props.book.author}</td>
            <td>{props.book.category}</td>
            <td><button type="button" className="btn btn-outline-secondary" disabled={props.book.status == "unavailable"} onClick={btnHandler}>{props.book.status}</button></td>            
        </tr>
    )
}
export default BookRow;