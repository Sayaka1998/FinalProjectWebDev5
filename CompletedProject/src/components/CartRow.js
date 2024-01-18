function CartRow(props) {
    const delBook = () => { // return the selected book data to the book list
        props.rmv(props.idx);
    }
    return(
        <tr>
            <td>{props.book.isbn}</td>
            <td>{props.book.bname}</td>
            <td>{props.book.author}</td>
            <td>{props.book.category}</td>  
            <td><button type="button" className="btn btn-outline-secondary" onClick={delBook}>Delete</button>
            </td>        
        </tr>
    )
}
export default CartRow;