    //Original:  Simon Tneoh (tneohcb@pc.jaring.my)
    //This script and many more are available free online at
    //The JavaScript Source!! http://javascript.internet.com
    var Cards = new makeArray(5);
    Cards[0] = new CardType("MasterCard", "51,52,53,54,55", "16");
    var MasterCard = Cards[0];
    Cards[1] = new CardType("VisaCard", "4", "13,16");
    var VisaCard = Cards[1];
    Cards[2] = new CardType("AmExCard", "34,37", "15");
    var AmExCard = Cards[2];
    Cards[3] = new CardType("DiscoverCard", "6011", "16");
    var DiscoverCard = Cards[3];
    var LuhnCheckSum = Cards[4] = new CardType();

    /*************************************************************************\
    CheckCardNumber(form)
    function called when users click the "check" button.
    /*************************************************************************/
    function validate(form){
        var retval = validateCreditCard(form);
        if (retval){
             //The rest of the checks...
            if (form.tax_exempt_id) {
                if (form.tax_exempt_id.value == "") {
                    form.tax_exempt_id.focus();
                    alert("You must enter a tax exempt number or reseller id");
                    return false;
                }
            }

            if (form.cvv.value == "") {
                form.cvv.focus();
                alert("You must enter a CVV #");
                return false;
            }
//                        if (form.first_name.value == "") {
//                            form.first_name.focus();
//                            alert("You must enter a First Name");
//                            return false;
//                        }
//                        if (form.last_name.value == "") {
//                            form.last_name.focus();
//                            alert("You must enter a Last Name");
//                            return false;
//                        }
            return true;

        }else{
            return false;
        }
        return true;
    }   

    
    function validateCreditCard(form, cc_type_field, cc_number_field, cc_expiry_month_field, cc_expiry_year_field, cvv_field) {
        var cc_type_field = (cc_type_field == null) ? "cc_type" : cc_type_field;
        var cc_number_field = (cc_number_field == null) ? "cc_number" : cc_number_field;
        var cc_expiry_month_field = (cc_expiry_month_field == null) ? "expiration_month" : cc_expiry_month_field;
        var cc_expiry_year_field = (cc_expiry_year_field == null) ? "expiration_year" : cc_expiry_year_field;
        var cc_cvv_field = (cc_cvv_field == null) ? "cvv" : cc_cvv_field;
        var tmpyear;
        ccNumber = document.getElementById(cc_number_field);
        ccType = document.getElementById(cc_type_field);
        ccExpiryMonth = document.getElementById(cc_expiry_month_field);
        ccExpiryYear = document.getElementById(cc_expiry_year_field);
        ccCVV = document.getElementById(cc_cvv_field);
        if (ccNumber.value.length == 0){ 
            alert("Please enter a valid Card Number.");
            ccNumber.focus();
            return false;
        }
        
        tmpyear = ccExpiryYear.options[ccExpiryYear.selectedIndex].value;
        tmpmonth = ccExpiryMonth.options[ccExpiryMonth.selectedIndex].value;
        if (!(new CardType()).isExpiryDate(tmpyear, tmpmonth)) {
            alert("This card has already expired.");
            return false;
        }
        card = ccType.options[ccType.selectedIndex].value;
        var retval = eval(card + ".checkCardNumber(\"" + ccNumber.value + "\", " + tmpyear + ", " + tmpmonth + ");");
        cardname = "";
        
        if (!retval) {
            // The cardnumber has the valid luhn checksum, but we want to know which
            // cardtype it belongs to.
            for (var n = 0; n < Cards.size; n++) {
                if (Cards[n].checkCardNumber(ccNumber.value, tmpyear, tmpmonth)) {
                    cardname = Cards[n].getCardType();
                    break;
                }
            }
            if (cardname.length > 0) {
                alert("This looks like a " + cardname + " number, not a " + card + " number.");
                return false;
            }
            else {
                alert("This card number is not valid.");
                return false;
            }
        }
        //alert(card);
        if (card=='AmExCard'  && ccCVV.value.length !=4){
            alert("Your CVV number for an American Express card must be 4 digits.");
            ccCVV.focus();
            return false;
        }else if (card !='AmExCard' && ccCVV.value.length !=3){
            alert("Your CVV number for a Visa, Mastercard, or Discover card must be 3 digits.");
            ccCVV.focus();
            return false;
        }
        return true;
    }
/*************************************************************************\
Object CardType([String cardtype, String rules, String len, int year, 
                                        int month])
cardtype    : type of card, eg: MasterCard, Visa, etc.
rules       : rules of the cardnumber, eg: "4", "6011", "34,37".
len         : valid length of cardnumber, eg: "16,19", "13,16".
year        : year of expiry date.
month       : month of expiry date.
eg:
var VisaCard = new CardType("Visa", "4", "16");
var AmExCard = new CardType("AmEx", "34,37", "15");
/*************************************************************************/
function CardType() {
    var n;
    var argv = CardType.arguments;
    var argc = CardType.arguments.length;

    this.objname = "object CardType";

    var tmpcardtype = (argc > 0) ? argv[0] : "CardObject";
    var tmprules = (argc > 1) ? argv[1] : "0,1,2,3,4,5,6,7,8,9";
    var tmplen = (argc > 2) ? argv[2] : "13,14,15,16,19";

    this.setCardNumber = setCardNumber;  // set CardNumber method.
    this.setCardType = setCardType;  // setCardType method.
    this.setLen = setLen;  // setLen method.
    this.setRules = setRules;  // setRules method.
    this.setExpiryDate = setExpiryDate;  // setExpiryDate method.

    this.setCardType(tmpcardtype);
    this.setLen(tmplen);
    this.setRules(tmprules);
    if (argc > 4)
        this.setExpiryDate(argv[3], argv[4]);

    this.checkCardNumber = checkCardNumber;  // checkCardNumber method.
    this.getExpiryDate = getExpiryDate;  // getExpiryDate method.
    this.getCardType = getCardType;  // getCardType method.
    this.isCardNumber = isCardNumber;  // isCardNumber method.
    this.isExpiryDate = isExpiryDate;  // isExpiryDate method.
    this.luhnCheck = luhnCheck;// luhnCheck method.
    return this;
}

/*************************************************************************\
boolean checkCardNumber([String cardnumber, int year, int month])
return true if cardnumber pass the luhncheck and the expiry date is
valid, else return false.
\*************************************************************************/
function checkCardNumber() {
    var argv = checkCardNumber.arguments;
    var argc = checkCardNumber.arguments.length;
    var cardnumber = (argc > 0) ? argv[0] : this.cardnumber;
    var year = (argc > 1) ? argv[1] : this.year;
    var month = (argc > 2) ? argv[2] : this.month;
    
    this.setCardNumber(cardnumber);
    this.setExpiryDate(year, month);
    
    if (!this.isCardNumber())
        return false;
    if (!this.isExpiryDate())
        return false;
    
    return true;
}
/*************************************************************************\
String getCardType()
return the cardtype.
\*************************************************************************/
function getCardType() {
    return this.cardtype;
}
/*************************************************************************\
String getExpiryDate()
return the expiry date.
\*************************************************************************/
function getExpiryDate() {
    return this.month + "/" + this.year;
}
/*************************************************************************\
boolean isCardNumber([String cardnumber])
return true if cardnumber pass the luhncheck and the rules, else return
false.
\*************************************************************************/
function isCardNumber() {
    var argv = isCardNumber.arguments;
    var argc = isCardNumber.arguments.length;
    var cardnumber = (argc > 0) ? argv[0] : this.cardnumber;
    if (!this.luhnCheck())
        return false;
    
    for (var n = 0; n < this.len.size; n++)
        if (cardnumber.toString().length == this.len[n]) {
            for (var m = 0; m < this.rules.size; m++) {
                var headdigit = cardnumber.substring(0, this.rules[m].toString().length);
                if (headdigit == this.rules[m])
                    return true;
            }
            return false;
        }
    return false;
}

/*************************************************************************\
boolean isExpiryDate([int year, int month])
return true if the date is a valid expiry date,
else return false.
\*************************************************************************/
function isExpiryDate() {
    var argv = isExpiryDate.arguments;
    var argc = isExpiryDate.arguments.length;
    
    year = argc > 0 ? argv[0] : this.year;
    month = argc > 1 ? argv[1] : this.month;
    
    if (!isNum(year+""))
        return false;
    if (!isNum(month+""))
        return false;
    today = new Date();
    expiry = new Date(year, month);
    if (today.getTime() > expiry.getTime())
        return false;
    else
        return true;
}   

/*************************************************************************\
boolean isNum(String argvalue)
return true if argvalue contains only numeric characters,
else return false.
\*************************************************************************/
function isNum(argvalue) {
    argvalue = argvalue.toString();

    if (argvalue.length == 0)
        return false;

    for (var n = 0; n < argvalue.length; n++)
        if (argvalue.substring(n, n+1) < "0" || argvalue.substring(n, n+1) > "9")
            return false;

    return true;
}

/*************************************************************************\
boolean luhnCheck([String CardNumber])
return true if CardNumber pass the luhn check else return false.
Reference: http://www.ling.nwu.edu/~sburke/pub/luhn_lib.pl
\*************************************************************************/
function luhnCheck() {
    var argv = luhnCheck.arguments;
    var argc = luhnCheck.arguments.length;

    var CardNumber = argc > 0 ? argv[0] : this.cardnumber;

    if (! isNum(CardNumber)) {
        return false;
    }

    var no_digit = CardNumber.length;
    var oddoeven = no_digit & 1;
    var sum = 0;

    for (var count = 0; count < no_digit; count++) {
        var digit = parseInt(CardNumber.charAt(count));
        if (!((count & 1) ^ oddoeven)) {
        digit *= 2;
        if (digit > 9)
            digit -= 9;
        }
        sum += digit;
    }
    if (sum % 10 == 0)
        return true;
    else
        return false;
}

/*************************************************************************\
ArrayObject makeArray(int size)
return the array object in the size specified.
\*************************************************************************/
function makeArray(size) {
    this.size = size;
    return this;
}

/*************************************************************************\
CardType setCardNumber(cardnumber)
return the CardType object.
\*************************************************************************/
function setCardNumber(cardnumber) {
    this.cardnumber = cardnumber;
    return this;
}

/*************************************************************************\
CardType setCardType(cardtype)
return the CardType object.
\*************************************************************************/
function setCardType(cardtype) {
    this.cardtype = cardtype;
    return this;
}

/*************************************************************************\
CardType setExpiryDate(year, month)
return the CardType object.
\*************************************************************************/
function setExpiryDate(year, month) {
    this.year = year;
    this.month = month;
    return this;
}

/*************************************************************************\
CardType setLen(len)
return the CardType object.
\*************************************************************************/
function setLen(len) {
    // Create the len array.
    if (len.length == 0 || len == null)
        len = "13,14,15,16,19";
    
    var tmplen = len;
    n = 1;
    while (tmplen.indexOf(",") != -1) {
        tmplen = tmplen.substring(tmplen.indexOf(",") + 1, tmplen.length);
        n++;
    }
    this.len = new makeArray(n);
    n = 0;
    while (len.indexOf(",") != -1) {
        var tmpstr = len.substring(0, len.indexOf(","));
        this.len[n] = tmpstr;
        len = len.substring(len.indexOf(",") + 1, len.length);
        n++;
    }
    this.len[n] = len;
    return this;
}

/*************************************************************************\
CardType setRules()
return the CardType object.
\*************************************************************************/
function setRules(rules) {
    // Create the rules array.
    if (rules.length == 0 || rules == null)
        rules = "0,1,2,3,4,5,6,7,8,9";
      
    var tmprules = rules;
    n = 1;
    while (tmprules.indexOf(",") != -1) {
        tmprules = tmprules.substring(tmprules.indexOf(",") + 1, tmprules.length);
        n++;
    }
    this.rules = new makeArray(n);
    n = 0;
    while (rules.indexOf(",") != -1) {
        var tmpstr = rules.substring(0, rules.indexOf(","));
        this.rules[n] = tmpstr;
        rules = rules.substring(rules.indexOf(",") + 1, rules.length);
        n++;
    }
    this.rules[n] = rules;
    return this;
}
