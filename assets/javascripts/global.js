function formatDate(date) {
    var day = date.getDate();
    var month = date.getMonth() + 1; // Months are zero-based
    var year = date.getFullYear();
  
    // Ensure leading zeros for day and month if necessary
    day = day < 10 ? "0" + day : day;
    month = month < 10 ? "0" + month : month;
  
    // Return the formatted date string
    return day + "-" + month + "-" + year;
  }


  export { formatDate };
