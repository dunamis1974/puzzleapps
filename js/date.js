var month=new Array(12);
month[0]="Jan";
month[1]="Feb";
month[2]="Mar";
month[3]="Apr";
month[4]="May";
month[5]="Jun";
month[6]="Jul";
month[7]="Aug";
month[8]="Sep";
month[9]="Oct";
month[10]="Nov";
month[11]="Dec";

function time2date (rtime) {
    var theDate = new Date(rtime * 1000);
    dateString = month[theDate.getMonth()] + ' ' + theDate.getDate() + ', ' + theDate.getFullYear() ;
    return dateString;
}