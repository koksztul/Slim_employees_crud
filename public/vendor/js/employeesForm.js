$(document).ready(function(){
    $('#submitForm').click(function () {
       if (isPeselValid($("#pesel").val())) {
           $("#employeeForm").submit();
       } else {
           $(".alert").remove();
           $("#pesel").after('<div class="alert alert-danger" role="alert">Pesel is invalid</div>');
       }
   });
});

function isPeselValid(pesel) {
   if (pesel.length !== 11) {
       return false;
   }
   if (!/^\d+$/.test(pesel)) {
       return false;
   }
 
   let weights = [1, 3, 7, 9, 1, 3, 7, 9, 1, 3, 1];
   let digits = pesel.split('');
   let checksum = 0;
   for (let i = 0; i < digits.length; i++) {
       checksum += weights[i] * parseInt(digits[i]);
   }
 
   if (checksum % 10 !== 0) {
       return false;
   }
 
   let year = pesel.substr(0, 2);
   let month = pesel.substr(2, 2);
   let day = pesel.substr(4, 2);
 
   let century = pesel.substr(2, 1);
   century = (parseInt(century) + 2) % 10;
   century = Math.floor(century / 2) + 18;
 
   let fullYear = century + year;
   let transformedMonth = (parseInt(month) % 20).toString().padStart(2, '0');
 
   if (fullYear < 1800 || fullYear > 2299) {
       return false;
   }
 
   if (parseInt(transformedMonth) < 1 || parseInt(transformedMonth) > 12) {
       return false;
   }
 
   let daysInMonth = new Date(fullYear, parseInt(transformedMonth), 0).getDate();
   if (parseInt(day) < 1 || parseInt(day) > daysInMonth) {
       return false;
   }
   return true;
}