/* Masthan Swamy */
/* Sidebar Code Starts Here*/
$(document).ready(function()
{
    // Navigation Sidebar Code
    $(".navbtn").click(function()
    {
        let bar = $(".sidebar");
        $(bar).addClass("navbtnClick");
    });
    $("#navclose").click(function()
    {
        $(".sidebar").removeClass("navbtnClick");
    });



    // User Signup Starts Here
    $("#signupForm").submit(function()
    {
        let name = document.getElementById("name");
        let email = document.getElementById("email");
        let pwd = document.getElementById("passwd");
        let cpwd = document.getElementById("cpasswd");

        let check = true;

        if(!name.validity.valid || !email.validity.valid || !pwd.validity.valid)
        {
            $("#signupForm").addClass("was-validated");
            check=false;
        }
        else
        {
            $("#signupForm").removeClass("was-validated");
        }
        if(pwd.value !== cpwd.value || pwd.value==="" || cpwd.value==="")
        {
            $(".manual-valid-feedback").css("display","none");
            $(".manual-invalid-feedback").css("display","block");

            check = false;

            $("cpwdval").focus(function()
            {
                $("#cpasswd").css({"border-color":"red","boxShadow":"0 0 0 0.2rem rgba(255,0,0,0.25)"});
            });
        }
        else
        {
            $(".manual-invalid-feedback").css("display","none");
            $(".manual-valid-feedback").css("display","block");

            $("cpwdval").focus(function()
            {
                $("#cpasswd").css({"border-color":"green","boxShadow":"0 0 0 0.2rem rgba(0,255,0,0.25)"});
            });
        }
        if(!pwd.validity.valid)
        {
            $("#passwordTips").css("display", "block");
            check = false;
        }
        else
        {
            $("#passwordTips").css("display","none");
        }

        return(check);
    });

    // Worker Signup Starts Here
    $("#workerSignupForm").submit(function()
    {
        let name = document.getElementById("wname");
        let age = document.getElementById("wage");
        let mobile = document.getElementById("wmobile");
        let email = document.getElementById("wemail");
        let pwd = document.getElementById("wpasswd");
        let cpwd = document.getElementById("wcpasswd");
        let image = document.getElementById("wImage");

        let check = true;

        if(!name.validity.valid || !age.validity.valid || !mobile.validity.valid || !email.validity.valid || !pwd.validity.valid || !image.validity.valid)
        {
            $("#workerSignupForm").addClass("was-validated");
            check=false;
        }
        else
        {
            $("#workerSignupForm").removeClass("was-validated");
        }

        // File Validation
  /*      let file = document.getElementById("wImage");
        let filetype = file['type'];
        let validExts = ["image/png","image/jppg","image/jpeg"];
        if($.inArray(filetype,validExts)<0)
        {
            alert(1);
            $("#workerSignupForm").addClass("was-validated");
            check=false;
        }
        else
        {
            alert(2);
            $("#workerSignupForm").removeClass("was-validated");
        }
*/
        if(pwd.value !== cpwd.value || pwd.value==="" || cpwd.value==="")
        {
            $(".manual-valid-feedback").css("display","none");
            $(".manual-invalid-feedback").css("display","block");

            check = false;

            $("cpwdval").focus(function()
            {
                $("#cpasswd").css({"border-color":"red","boxShadow":"0 0 0 0.2rem rgba(255,0,0,0.25)"});
            });
        }
        else
        {
            $(".manual-invalid-feedback").css("display","none");
            $(".manual-valid-feedback").css("display","block");

            $("cpwdval").focus(function()
            {
                $("#cpasswd").css({"border-color":"green","boxShadow":"0 0 0 0.2rem rgba(0,255,0,0.25)"});
            });
        }
        if(!pwd.validity.valid)
        {
            $("#passwordTips").css("display", "block");
            check = false;
        }
        else
        {
            $("#passwordTips").css("display","none");
        }

        return check;
    });

    // Login Form Validation
    $("#loginForm").submit(function()
    {
        let username = document.getElementById("email");
        let password = document.getElementById("passwd");

        if(!username.validity.valid || !password.validity.valid)
        {
            $("#loginForm").addClass(" was-validated");
            return false;
        }
        return true;
    });

    // Worker Signup 2
    function getEnterPX(wiwidth)
    {
        let s1 = Number(wiwidth.substring(0,2));
        s1+=5;
        return s1+"px";
    }
    function getLeavePX(wiwidth)
    {
        let s1 = Number(wiwidth.substring(0,2));
        s1-=5;
        if(s1<51)
        {
            s1=51;
        }
        return s1+"px";
    }

    $(".workItem").mouseenter(function() {
        let wicon1 = $(this).children("i.main");
        let wicon2 = $(this).children("svg.main");

        let wicon;
        if (wicon1.length === 0)
        {
            let wiwidth = $(wicon2).css("width");
            let s1 = getEnterPX(wiwidth);
            $(wicon2).css({"width":s1,"height":s1});
        }
        else
        {
            let wiwidth = $(wicon1).css("font-size");
            $(wicon1).css("font-size",getEnterPX(wiwidth));
        }
    });
    $(".workItem").mouseleave(function()
    {
        let wicon1 = $(this).children("i.main");
        let wicon2 = $(this).children("svg.main");

        let wicon;
        if (wicon1.length === 0)
        {
            let wiwidth = $(wicon2).css("width");
            let s1 = getLeavePX(wiwidth);
            $(wicon2).css({"width":s1,"height":s1});
        }
        else
        {
            let wiwidth = $(wicon1).css("font-size");
            $(wicon1).css("font-size",getLeavePX(wiwidth));
        }
    });

});
// Worker Signup 2
function searchWork()
{
    let val = document.getElementById("searchWork").value;
    let wicons = $(".workItem");
    for(i=0;i<wicons.length;i++)
    {
        let title = $(wicons[i]).attr("title");

        val = val.toUpperCase();
        title = title.toUpperCase();

        if(title.indexOf(val)>-1)
        {
            $(wicons[i]).css("display","");
        }
        else
        {
            $(wicons[i]).css("display","none");
        }
    }
}