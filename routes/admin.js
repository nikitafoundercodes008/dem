const express = require('express');
var session=require('express-session');
const router = express.Router();

const { getsettings,admin_login,dynamic_dashboard,dynamic_sidebar,update_business_setup,get_games,match_details,contest_details,get_teams,get_designation,get_all_users,add_wallet,min_amount,users_doc,doc_byid,active,deactive,get_withdrawal_request,reject_withdrawal_request,get_transactionbyid,get_banner,deletes } = require('./admin_model'); // Import the function from admin_model

const myURL = new URL('https://khiladi11.live/');

// Define API routes
router.get('/deactive/users_doc/:id',async(req,res)=>{
    id=req.params.id;
    tablename="doc_verification";
    deletecol=await deactive(req,tablename,id); 
    res.redirect(`/prodsite/users/doc-verification`);
    
});

router.get('/active/users_doc/:id',async(req,res)=>{
    id=req.params.id;
    tablename="doc_verification";
    deletecol=await active(req,tablename,id); 
    res.redirect(`/prodsite/users/doc-verification`);
    
});

router.get('/delete/banners/:id/:type',async(req,res)=>{
    id=req.params.id;
    type=req.params.type;
    tablename="banners";
    deletecol=await deletes(req,tablename,id); 
    res.redirect(`/prodsite/banners-list/${type}`);
    
});

router.get('/', async(req, res) => {
    try{
        baseurl=myURL.href;
        userid=req.session.userid;
        console.log('======================================');
        console.log(userid);
        console.log('======================================');
        if(userid){
            res.redirect(`${baseurl}prodsite/dashboard`);
        }
        else{
            const settings = await getsettings(req);
            console.log(settings);
            data={ 
                baseurl: myURL.href,
                logo:settings[2].value,
                project_name:settings[3].value,
                bg_cover:settings[4].value
            };
            res.render('login.ejs',data);
        }
    }
    catch (error) {
        console.error('Error:', error.message);
        res.status(500).send('Internal Server Error');
    }
});

router.post('/login', async(req, res) => {
    try{
        baseurl=myURL.href;
        email=req.body.email;
        password=req.body.password;
      
        const data = await admin_login(req,email,password);
        if(data.length>0)
        {
            var roleid=data[0]['roleid'];
            var fullname=data[0]['firstname']+" "+data[0]['lastname'];
            var profileimg=data[0]['profile'];
            var userid=data[0]['id'];
            var useremail=data[0]['email'];

            req.session.username=fullname;
            req.session.profileimg=profileimg;
            req.session.userid=userid;
            req.session.roleid=roleid;
            req.session.useremail=useremail;
            
            res.redirect(`${baseurl}prodsite/dashboard`);
        }
        else{
            console.log(`Credentials Error`);
        }

    }
    catch (error) {
        console.error('Error:', error.message);
        res.status(500).send('Internal Server Error');
    }
});

router.get('/logout',  function (req, res, next)  {
        baseurl=myURL.href;
        req.session.loggedin = false;
        req.session.destroy();
        res.redirect('/prodsite');

});

router.get('/dashboard', async(req, res) => {
    try{
        baseurl=myURL.href;
        username=req.session.username;
        profileimg=req.session.profileimg;
        userid=req.session.userid;
        roleid=req.session.roleid;
        useremail=req.session.useremail;
        console.log(userid)
        if(userid){
            const settings = await getsettings(req);
            const project_name=settings[3].value
            const copyright=settings[5].value
            
            const dynamicdashboard = await dynamic_dashboard(req,roleid);
            const dynamicsidebar = await dynamic_sidebar(req,roleid);
            
            console.log('======================================');
            console.log(userid);
            console.log('======================================');
            data={ 
                baseurl: myURL.href,
                username:username,
                userid:userid,
                roleid:roleid,
                useremail:useremail,
                project_name:project_name,
                dashboarddata:dynamicdashboard,
                sidebarddata:dynamicsidebar,
                copyright:copyright
            }
            res.render('dashboard.ejs',data)
            
        }
        else{
            res.redirect(`${baseurl}prodsite`);
        }
        
    }
    catch (error) {
        console.error('Error:', error.message);
        res.status(500).send('Internal Server Error');
    }
});

router.get('/business-setup', async(req, res) => {
    try{
        baseurl=myURL.href;
        username=req.session.username;
        profileimg=req.session.profileimg;
        userid=req.session.userid;
        roleid=req.session.roleid;
        useremail=req.session.useremail;
        console.log(userid)
        if(userid){
            const settings = await getsettings(req);
            const project_name=settings[3].value
            const copyright=settings[5].value
            
            const dynamicdashboard = await dynamic_dashboard(req,roleid);
            const dynamicsidebar = await dynamic_sidebar(req,roleid);
            
            console.log('======================================');
            console.log(userid);
            console.log('======================================');
            data={ 
                baseurl: myURL.href,
                username:username,
                userid:userid,
                roleid:roleid,
                useremail:useremail,
                project_name:project_name,
                dashboarddata:dynamicdashboard,
                sidebarddata:dynamicsidebar,
                copyright:copyright,
                data:settings
            }
            res.render('tables/business_settings.ejs',data)
            
        }
        else{
            res.redirect(`${baseurl}prodsite`);
        }
        
    }
    catch (error) {
        console.error('Error:', error.message);
        res.status(500).send('Internal Server Error');
    }
});

router.post('/update/business-setup', async(req, res) => {
    try{
        baseurl=myURL.href;
        
        userid=req.session.userid;
        
        console.log(userid)
        id=req.body.id;
        value=req.body.name;
        if(userid){
            if(id=='3') // Logo Update
            {
                const update_settings = await update_business_setup(req,id,value);
            }
            else if(id=='14')
            {
                merchant_id=req.body.merchant_id;
                merchant_token=req.body.merchant_token;
                account_number=req.body.account_number;
                ifsc_code=req.body.ifsc_code;
                bank_name=req.body.bank_name;
                acc_hplder_name=req.body.acc_hplder_name;
                contact=req.body.contact;
                
                values={
                    "merchant_id":merchant_id.toString() , 
                    "merchant_token":merchant_token.toString() , 
                    "account_no":account_number.toString() , 
                    "ifsccode":ifsc_code.toString() ,
                    "bankname":bank_name.toString() , 
                    "account_holder_name":acc_hplder_name.toString() ,
                    "contact":contact.toString() 
                }
                
                datas=JSON.stringify(values);
                
                const update_settings = await update_business_setup(req,id,datas);
            }
            else{
                const update_settings = await update_business_setup(req,id,value);
            }
            
            res.redirect(`${baseurl}prodsite/business-setup`);
            
        }
        else{
            res.redirect(`${baseurl}prodsite`);
        }
        
    }
    catch (error) {
        console.error('Error:', error.message);
        res.status(500).send('Internal Server Error');
    }
});

router.get('/banners-list/:type', async(req, res) => {
    try{
        baseurl=myURL.href;
        username=req.session.username;
        profileimg=req.session.profileimg;
        userid=req.session.userid;
        roleid=req.session.roleid;
        useremail=req.session.useremail;
        console.log(userid)
        
        type=req.params.type;
        if(userid){
            const settings = await getsettings(req);
            const project_name=settings[3].value
            const copyright=settings[5].value
            
            const dynamicdashboard = await dynamic_dashboard(req,roleid);
            const dynamicsidebar = await dynamic_sidebar(req,roleid);
            
            
            const games = await get_banner(req,type);
            
            console.log('======================================');
            console.log(userid);
            console.log('======================================');
            data={ 
                baseurl: myURL.href,
                username:username,
                userid:userid,
                roleid:roleid,
                useremail:useremail,
                project_name:project_name,
                dashboarddata:dynamicdashboard,
                sidebarddata:dynamicsidebar,
                copyright:copyright,
                data:games,
                type:type
            }
            res.render('tables/banner_list.ejs',data)
            
        }
        else{
            res.redirect(`${baseurl}prodsite`);
        }
    }
    catch (error) {
        console.error('Error:', error.message);
        res.status(500).send('Internal Server Error');
    }
});

router.get('/game-list', async(req, res) => {
    try{
        baseurl=myURL.href;
        username=req.session.username;
        profileimg=req.session.profileimg;
        userid=req.session.userid;
        roleid=req.session.roleid;
        useremail=req.session.useremail;
        console.log(userid)
        if(userid){
            const settings = await getsettings(req);
            const project_name=settings[3].value
            const copyright=settings[5].value
            
            const dynamicdashboard = await dynamic_dashboard(req,roleid);
            const dynamicsidebar = await dynamic_sidebar(req,roleid);
            
            const games = await get_games(req);
            
            console.log('======================================');
            console.log(userid);
            console.log('======================================');
            data={ 
                baseurl: myURL.href,
                username:username,
                userid:userid,
                roleid:roleid,
                useremail:useremail,
                project_name:project_name,
                dashboarddata:dynamicdashboard,
                sidebarddata:dynamicsidebar,
                copyright:copyright,
                data:games
            }
            res.render('tables/game_list.ejs',data)
            
        }
        else{
            res.redirect(`${baseurl}prodsite`);
        }
    }
    catch (error) {
        console.error('Error:', error.message);
        res.status(500).send('Internal Server Error');
    }
});

router.get('/match-details/:gameid/:type', async(req, res) => {
    try{
        baseurl=myURL.href;
        username=req.session.username;
        profileimg=req.session.profileimg;
        userid=req.session.userid;
        roleid=req.session.roleid;
        useremail=req.session.useremail;
        
        gameid=req.params.gameid;
        type=req.params.type; //1=>Upcomming,2=>Live,3=>Complete
        console.log(userid);
        if(userid){
            const settings = await getsettings(req);
            const project_name=settings[3].value
            const copyright=settings[5].value
            
            const dynamicdashboard = await dynamic_dashboard(req,roleid);
            const dynamicsidebar = await dynamic_sidebar(req,roleid);
            
            const match_detail = await match_details(req,gameid,type);
            
            if(type=='1')
            {
                matchtype="Upcoming Match Details";
            }
            if(type=='2')
            {
                matchtype="Live Match Details";
            }
            if(type=='3')
            {
                matchtype="Complete Match Details";
            }
            
            data={ 
                baseurl: myURL.href,
                username:username,
                userid:userid,
                roleid:roleid,
                useremail:useremail,
                project_name:project_name,
                dashboarddata:dynamicdashboard,
                sidebarddata:dynamicsidebar,
                copyright:copyright,
                type:type,
                data:match_detail,
                matchtype:matchtype
            }
            res.render('tables/match_details.ejs',data)
            
        }
        else{
            res.redirect(`${baseurl}prodsite`);
        }
    }
    catch (error) {
        console.error('Error:', error.message);
        res.status(500).send('Internal Server Error');
    }
});

router.get('/contest-default/', async(req, res) => {
    try{
        baseurl=myURL.href;
        username=req.session.username;
        profileimg=req.session.profileimg;
        userid=req.session.userid;
        roleid=req.session.roleid;
        useremail=req.session.useremail;
        
        type="Default Contest"; //1=>Upcomming,2=>Live,3=>Complete
        console.log(userid);
        if(userid){
            const settings = await getsettings(req);
            const project_name=settings[3].value
            const copyright=settings[5].value
            
            const dynamicdashboard = await dynamic_dashboard(req,roleid);
            const dynamicsidebar = await dynamic_sidebar(req,roleid);
            
            const contest_detail = await contest_details(req,'');
            
            
            data={ 
                baseurl: myURL.href,
                username:username,
                userid:userid,
                roleid:roleid,
                useremail:useremail,
                project_name:project_name,
                dashboarddata:dynamicdashboard,
                sidebarddata:dynamicsidebar,
                copyright:copyright,
                type:type,
                data:contest_detail,
            }
            res.render('tables/contest_list.ejs',data)
            
        }
        else{
            res.redirect(`${baseurl}prodsite`);
        }
    }
    catch (error) {
        console.error('Error:', error.message);
        res.status(500).send('Internal Server Error');
    }
});

// router.get('/contest-default/', async(req, res) => {
//     try{
//         baseurl=myURL.href;
//         username=req.session.username;
//         profileimg=req.session.profileimg;
//         userid=req.session.userid;
//         roleid=req.session.roleid;
//         useremail=req.session.useremail;
        
//         type="Default Contest"; //1=>Upcomming,2=>Live,3=>Complete
//         console.log(userid);
//         if(userid){
//             const settings = await getsettings(req);
//             const project_name=settings[3].value
//             const copyright=settings[5].value
            
//             const dynamicdashboard = await dynamic_dashboard(req,roleid);
//             const dynamicsidebar = await dynamic_sidebar(req,roleid);
            
//             const contest_detail = await contest_details(req,'');
            
            
//             data={ 
//                 baseurl: myURL.href,
//                 username:username,
//                 userid:userid,
//                 roleid:roleid,
//                 useremail:useremail,
//                 project_name:project_name,
//                 dashboarddata:dynamicdashboard,
//                 sidebarddata:dynamicsidebar,
//                 copyright:copyright,
//                 type:type,
//                 data:contest_detail,
//             }
//             res.render('tables/contest_list.ejs',data)
            
//         }
//         else{
//             res.redirect(`${baseurl}prodsite`);
//         }
//     }
//     catch (error) {
//         console.error('Error:', error.message);
//         res.status(500).send('Internal Server Error');
//     }
// });

router.get('/teams-list', async(req, res) => {
    try{
        baseurl=myURL.href;
        username=req.session.username;
        profileimg=req.session.profileimg;
        userid=req.session.userid;
        roleid=req.session.roleid;
        useremail=req.session.useremail;
        console.log(userid)
        if(userid){
            const settings = await getsettings(req);
            const project_name=settings[3].value
            const copyright=settings[5].value
            
            const dynamicdashboard = await dynamic_dashboard(req,roleid);
            const dynamicsidebar = await dynamic_sidebar(req,roleid);
            
            const teams = await get_teams(req);
            
            console.log('======================================');
            console.log(userid);
            console.log('======================================');
            data={ 
                baseurl: myURL.href,
                username:username,
                userid:userid,
                roleid:roleid,
                useremail:useremail,
                project_name:project_name,
                dashboarddata:dynamicdashboard,
                sidebarddata:dynamicsidebar,
                copyright:copyright,
                data:teams
            }
            res.render('tables/teams_list.ejs',data)
            
        }
        else{
            res.redirect(`${baseurl}prodsite`);
        }
    }
    catch (error) {
        console.error('Error:', error.message);
        res.status(500).send('Internal Server Error');
    }
});

router.get('/designation-list/:gameid', async(req, res) => {
    try{
        baseurl=myURL.href;
        username=req.session.username;
        profileimg=req.session.profileimg;
        userid=req.session.userid;
        roleid=req.session.roleid;
        useremail=req.session.useremail;
        
        gameid=req.params.gameid;
        console.log(userid)
        if(userid){
            const settings = await getsettings(req);
            const project_name=settings[3].value
            const copyright=settings[5].value
            
            const dynamicdashboard = await dynamic_dashboard(req,roleid);
            const dynamicsidebar = await dynamic_sidebar(req,roleid);
            
            const designation = await get_designation(req,gameid);
            
            console.log('======================================');
            console.log(userid);
            console.log('======================================');
            data={ 
                baseurl: myURL.href,
                username:username,
                userid:userid,
                roleid:roleid,
                useremail:useremail,
                project_name:project_name,
                dashboarddata:dynamicdashboard,
                sidebarddata:dynamicsidebar,
                copyright:copyright,
                data:designation
            }
            res.render('tables/designation_list.ejs',data)
            
        }
        else{
            res.redirect(`${baseurl}prodsite`);
        }
    }
    catch (error) {
        console.error('Error:', error.message);
        res.status(500).send('Internal Server Error');
    }
});

router.get('/users-list', async(req, res) => {
    try{
        baseurl=myURL.href;
        username=req.session.username;
        profileimg=req.session.profileimg;
        userid=req.session.userid;
        roleid=req.session.roleid;
        useremail=req.session.useremail;
        console.log(userid)
        if(userid){
            const settings = await getsettings(req);
            const project_name=settings[3].value
            const copyright=settings[5].value
            
            const dynamicdashboard = await dynamic_dashboard(req,roleid);
            const dynamicsidebar = await dynamic_sidebar(req,roleid);
            
            const get_users = await get_all_users(req);
            
            console.log('======================================');
            console.log(userid);
            console.log('======================================');
            data={ 
                baseurl: myURL.href,
                username:username,
                userid:userid,
                roleid:roleid,
                useremail:useremail,
                project_name:project_name,
                dashboarddata:dynamicdashboard,
                sidebarddata:dynamicsidebar,
                copyright:copyright,
                data:get_users
            }
            res.render('tables/users_list.ejs',data)
            
        }
        else{
            res.redirect(`${baseurl}prodsite`);
        }
    }
    catch (error) {
        console.error('Error:', error.message);
        res.status(500).send('Internal Server Error');
    }
});

router.post('/user/add_wallet', async (req, res) => {
    try {
        payload=req.body;
        
        const userid= payload.userid;
        const amount= payload.amount
         if(!userid)
        {
            response={
                'msg':"Userid Is Required",
                "status":"400"
            }
            res.status(200).send(response)
            return;
        }
        console.log(amount)
        if(!amount)
        {
            response={
                'msg':"Amount Is Required",
                "status":"400"
            }
            res.status(200).send(response)
            return;
        }
        const row2=await min_amount(req);
        const minamount=row2[0].value;
        console.log(minamount);
        if(amount<parseInt(minamount))
        {
            response={
                'msg':`Minimum Limit is ₹ ${minamount}`,
                "status":"400"
            }
            res.status(200).send(response)
            return;
        }
        
        // const row34=await add_wallet(req,userid,amount);
        
        sql=`UPDATE user_details SET wallet=wallet+${amount},unutiliesed_wallet=unutiliesed_wallet+${amount} WHERE user_id='${userid}'`;
        console.log(sql);
        const [rows] = await req.db.query(sql);
        
        const [rows2] = await req.db.query(`INSERT INTO transactions(userid, amount, type, sub_type, status) VALUES ('${userid}','${amount}','1','1','1')`);
        
        response={
            'msg':`Amount ₹ ${amount} Added Successfully`,
            "status":"200"
        }
        res.status(200).send(response)
        return;
    } catch (err) {
        res.status(200).json({ error: err.message });
    }
});

router.get('/users/doc-verification', async(req, res) => {
    try{
        baseurl=myURL.href;
        username=req.session.username;
        profileimg=req.session.profileimg;
        userid=req.session.userid;
        roleid=req.session.roleid;
        useremail=req.session.useremail;
        console.log(userid)
        if(userid){
            const settings = await getsettings(req);
            const project_name=settings[3].value
            const copyright=settings[5].value
            
            const dynamicdashboard = await dynamic_dashboard(req,roleid);
            const dynamicsidebar = await dynamic_sidebar(req,roleid);
            
            const datas = await users_doc(req);
            
            console.log('======================================');
            console.log(userid);
            console.log('======================================');
            data={ 
                baseurl: myURL.href,
                username:username,
                userid:userid,
                roleid:roleid,
                useremail:useremail,
                project_name:project_name,
                dashboarddata:dynamicdashboard,
                sidebarddata:dynamicsidebar,
                copyright:copyright,
                data:datas
            }
            res.render('tables/users_doc_verification.ejs',data)
            
        }
        else{
            res.redirect(`${baseurl}prodsite`);
        }
    }
    catch (error) {
        console.error('Error:', error.message);
        res.status(500).send('Internal Server Error');
    }
});

router.get('/documents/:id', async(req, res) => {
    try{
        baseurl=myURL.href;
        username=req.session.username;
        profileimg=req.session.profileimg;
        userid=req.session.userid;
        roleid=req.session.roleid;
        useremail=req.session.useremail;
        
        id=req.params.id;
        console.log(userid)
        if(userid){
            const settings = await getsettings(req);
            const project_name=settings[3].value
            const copyright=settings[5].value
            
            const dynamicdashboard = await dynamic_dashboard(req,roleid);
            const dynamicsidebar = await dynamic_sidebar(req,roleid);
            
            const datas = await doc_byid(req,id);
            if(datas.length=='0')
            {
                images=[]
                ids="";
            }
            else
            {
                image=datas[0].doc_image;
                imagesdata=JSON.parse(image);
                images=imagesdata.images;
                ids=datas[0].docid;
            }
            
            console.log('======================================');
            console.log(image);
            console.log('======================================');
            console.log(images);
            console.log('======================================');
            data={ 
                baseurl: myURL.href,
                username:username,
                userid:userid,
                roleid:roleid,
                useremail:useremail,
                project_name:project_name,
                dashboarddata:dynamicdashboard,
                sidebarddata:dynamicsidebar,
                copyright:copyright,
                data:images,
                id:ids
            }
            res.render('tables/users_document.ejs',data)
            
        }
        else{
            res.redirect(`${baseurl}prodsite`);
        }
    }
    catch (error) {
        console.error('Error:', error.message);
        res.status(500).send('Internal Server Error');
    }
});

router.get('/withdrawal-request/:type', async(req, res) => {
    try{
        baseurl=myURL.href;
        username=req.session.username;
        profileimg=req.session.profileimg;
        userid=req.session.userid;
        roleid=req.session.roleid;
        useremail=req.session.useremail;
        console.log(userid)
        
        const type= req.params.type; // 0=>Pending,1=>Success,2=>Reject
        if(userid){
            const settings = await getsettings(req);
            const project_name=settings[3].value
            const copyright=settings[5].value
            
            const dynamicdashboard = await dynamic_dashboard(req,roleid);
            const dynamicsidebar = await dynamic_sidebar(req,roleid);
            
            const withdrawal_request = await get_withdrawal_request(req,type);
            
            console.log('======================================');
            console.log(userid);
            console.log('======================================');
            
            if(type=='0')
            {
                types="Withdrawal Request Pending";
            }
            if(type=='1')
            {
                types="Withdrawal Request Successfully";
            }
            if(type=='2')
            {
                types="Withdrawal Request Rejected";
            }
            data={ 
                baseurl: myURL.href,
                username:username,
                userid:userid,
                roleid:roleid,
                useremail:useremail,
                project_name:project_name,
                dashboarddata:dynamicdashboard,
                sidebarddata:dynamicsidebar,
                copyright:copyright,
                data:withdrawal_request,
                type:types,
                types:type
            }
            res.render('tables/withdrawal_request.ejs',data)
            
        }
        else{
            res.redirect(`${baseurl}prodsite`);
        }
    }
    catch (error) {
        console.error('Error:', error.message);
        res.status(500).send('Internal Server Error');
    }
});

router.get('/reject_withdrawal-request/:userid/:transactionid', async(req, res) => {
    try{
        baseurl=myURL.href;
        
        userid=req.session.userid;
        
        
        const userids= req.params.userid; // 0=>Pending,1=>Success,2=>Reject
        const transactionid= req.params.transactionid;
        
        const transaction_detailbyid= await get_transactionbyid(req,transactionid);
        
        if(transaction_detailbyid.length=='0')
        {
            amount="0"
        }
        else{
            amount=transaction_detailbyid[0].amount;
        }
        if(userid){
            
            
            const withdrawal_request = await reject_withdrawal_request(req,transactionid,amount,userids);
            
            res.redirect(`${baseurl}prodsite/withdrawal-request/0`);
        }
        else{
            res.redirect(`${baseurl}prodsite`);
        }
    }
    catch (error) {
        console.error('Error:', error.message);
        res.status(500).send('Internal Server Error');
    }
});

// Add more API routes as needed

module.exports = router
