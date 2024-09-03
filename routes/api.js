const express = require('express');
const fs = require('fs');
const router = express.Router();
const { my_upcoming_matches,send_otp,verify_otp,exist_user,exist_mobile,get_profile,update_profile,update_profile_image,update_users_dac,min_amount,add_wallet,get_transaction_type,get_transaction,get_games,my_live_matches,my_complete_matches,match_status,contest_list,mycontest_list,upcoming_matches,contest_winning,match_details_byid,contest_filter,get_banner,withdraw_wallet,get_withdraw_amount,min_withdrawal_amount,contestFilterWiseData,get_players_bySeries,my_date_wiserefreals,direct_subordinate,team_subordinate,my_refreals,my_tear_wise_subordinatedata,exist_cupon,generateRandomString,signup_transactions,referal_transactions,update_payin_status,get_settings,how_to_play,get_notifications,save_viewed_notifications } = require('./api_model'); // Import the function from api_model
const axios = require('axios');



// Define API routes

router.get('/send_otp/:mobile/:type', async (req, res) => {
    try {
        const mobile = req.params.mobile;
        const type = req.params.type;
        const response2 = await axios.get(`https://otp.fctechteam.org/send_otp.php?mode=live&digit=6&mobile=${mobile}`);
        console.log(response2.data);
        otp=response2.data.otp;
        
        if(type=='1') // Though Login
        {
            const row2 = await exist_mobile(req,mobile);
            if(row2.length==0){
                const query = await req.db.query(`INSERT INTO users(mobile,otp) VALUES ('${mobile}','${otp}')`);
            }
            else{
                const rows = await send_otp(req,otp,mobile);
            }
        }
        if(type=='2') // Through Update Mobile
        {
            const rows = await send_otp(req,otp,mobile);
        }
        
        response={
            'otp':otp.toString(),
            'msg':"Otp Send Successfully",
            'status':"200"
        }
        res.status(200).send(response)
        return;
    } catch (error) {
        console.error("Error sending SMS:", error);
    }
});

router.get('/verify_otp/:mobile/:otp/:type', async (req, res) => {
    try {
        const mobile = req.params.mobile;
        const otp = req.params.otp;
        const type = req.params.type;
        const cupon = req.query.cupon;
        
        const response2 = await axios.get(`https://otp.fctechteam.org/verifyotp.php?mobile=${mobile}&otp=${otp}`);
        console.log(response2.data);
        status=response2.data.error;
        if(status=='200')
        {
            const rows = await verify_otp(req,otp,mobile);
            
            if(type=='1')
            {
                
                user_status=rows[0].status;
                userid=rows[0].id;
                const [rows5] = await req.db.query(`UPDATE users SET status='1' WHERE id='${userid}'`);
                const rows2 = await get_profile(req, userid);
                if(user_status=='0')
                {
                    const invite_code=await generateRandomString(6)
        
                    sql=`INSERT INTO user_details(user_id, wallet,bonus_wallet,Invitation_code) VALUES ('${userid}',(SELECT value FROM settings WHERE name='Signup Bonus' && status='1'),(SELECT value FROM settings WHERE name='Signup Bonus' && status='1'),'${invite_code}')`;
                    console.log(sql);
                    const [rows8] = await req.db.query(sql);
                    
                    const ext_cupon = await exist_cupon(req,cupon);
                    if(ext_cupon.length=='0')
                    {
                        refered_by="0";
                    }
                    else
                    {
                        refered_by=ext_cupon[0].user_id;
                    }
                    
                    const rows3 = await signup_transactions(req,userid);
        
                    const rows4 = await referal_transactions(req,userid,refered_by);
                    
                    data=rows2[0];
                    msg="You are Not register";
                    respstatus=400;
                }
                if(user_status=='1')
                {
                    
                    msg="Login Successfully";
                    respstatus=200;
                    data=rows2[0];
                }
                console.log('Ram')
            }
            if(type=='2')
            {
                const userid = req.query.userid;
                if(!userid)
                {
                    response={
                        'msg':"Userid Is Required",
                        "status":"400"
                    }
                    res.status(200).send(response)
                    return;
                }
                const rows3 = await exist_mobile(req, mobile);
                if(rows3.length=='1')
                {
                   response={
                        'msg':"This Contact is Asociated with another user",
                        'status':"300"
                    }
                    res.status(200).send(response)
                    return; 
                }
                const [rows] = await req.db.query(`UPDATE users SET mobile='${mobile}' WHERE id='${userid}'`);
                response={
                    'msg':"Mobile Updated Successfully",
                    'status':"200"
                }
                res.status(200).send(response)
                return;
            }
        }
        if(status=='400')
        {
            data=[];
            respstatus="300";
            msg=response2.data.msg;
            console.log('Mohan')
        }
        
        
        response={
            'msg':`${msg}`,
            'status':respstatus.toString(),
            'data':data
        }
        res.status(200).send(response)
        return;
    } catch (error) {
        console.error("Error sending SMS:", error);
    }
});

router.post('/check/invite_cupon', async (req, res) => {
    try {
        payload=req.body;
        const cupon = payload.cupon;
        if(!cupon)
        {
            response={
                'msg':"Cupon Is Required",
                "status":"400"
            }
            res.status(200).send(response)
            return;
        }
        
        const rows = await exist_cupon(req,cupon);
        if(rows.length==0)
        {
            userid="";
            apistatus=400;
            msg="Invalid code! Enter a Valid Code";
        }
        else{
            userid =rows[0].user_id ;
            console.log(userid)
            msg="Cupon Applied Successfully";
            apistatus=200;
        }
        
        response={
            'cupon':cupon,
            'msg':msg,
            'status':apistatus.toString()
            // 'data':data,
        }
        res.status(200).send(response)
        return;
    } catch (error) {
        console.error("Error sending SMS:", error);
    }
});

router.post('/user/login', async (req, res) => {
    try {
        payload=req.body;
        const mobile = payload.mobile;
        if(!mobile)
        {
            response={
                'msg':"Mobile Is Required",
                "status":"400"
            }
            res.status(200).send(response)
            return;
        }
        
        const rows = await exist_user(req,mobile);
        if(rows.length==0)
        {
            data=[];
            apistatus=400;
            msg="You are not Register";
        }
        else{
            const userid =rows[0].id ;
            msg="Login Successfully";
            apistatus=200;
            
            const rows2 = await get_profile(req, userid);
            data=rows2[0];
        }
        
        response={
            'msg':msg,
            'status':apistatus.toString()
            // 'data':data,
        }
        res.status(200).send(response)
        return;
    } catch (error) {
        console.error("Error sending SMS:", error);
    }
});

router.get('/get_profile/:userid', async (req, res) => {
  try {
    const userid = req.params.userid;
    const rows = await get_profile(req, userid);
    if(rows.length=='0')
    {
        data=[];
        msg="No Data Found";
        apistatus=400;
    }
    else
    {
        data=rows[0];
        msg="Profile Get Successfully";
        apistatus=200;
    }
    
    const rows4 = await min_withdrawal_amount(req);
    if(rows4.length=='0')
    {
        data=[];
        msg="No Data Found";
        apistatus=400;
    }
    else
    {
        min_withdrawl_amt=rows4[0].value;
        msg="Profile Get Successfully";
        apistatus=200;
    }
    response={
        'data':data,
        'min_withdrawl_amt':min_withdrawl_amt,
        'msg': msg,
        'status':apistatus.toString()
    }
    res.status(200).send(response)
    return;
  } catch (err) {
    res.status(200).json({ error: err.message });
    return;
  }
});

router.post('/user/signup', async (req, res) => {
    try {
        payload=req.body;
        const name = payload.name;
        const mobile = payload.mobile;
        const cupon = payload.cupon;
        
        
        if(!mobile)
        {
            response={
                'msg':"Mobile Is Required",
                "status":"400"
            }
            res.status(200).send(response)
            return;
        }
        const ext_cupon = await exist_cupon(req,cupon);
        if(ext_cupon.length=='0')
        {
            refered_by="0";
        }
        else
        {
            refered_by=ext_cupon[0].user_id;
        }
        const row2 = await exist_mobile(req,mobile);
        const userid=row2[0].id;
        
        const insert_data = await update_profile(req,userid,name,'','','','1',refered_by);
        apistatus=200;
        response={
            'msg': "Inserted Successfully",
            'status':apistatus.toString()
        }
        res.status(200).send(response)
        return;
    } catch (err) {
        res.status(200).json({ error: err.message });
    }
});

router.post('/update/profile', async (req, res) => {
    try {
        payload=req.body;
        
        const name = payload.name;
        const userid= payload.userid;
        const dob= payload.dob;
        const gender= payload.gender;
        const age= payload.age;
        
        
        if(!userid)
        {
            response={
                'msg':"Userid Is Required",
                "status":"400"
            }
            res.status(200).send(response)
            return;
        }
        
        
        const insert_data = await update_profile(req,userid,name,dob,age,gender,'0','');
        apistatus=200;
        response={
            'msg': "Updated Successfully",
            'status':apistatus.toString()
        }
        res.status(apistatus).send(response)
        return;
    } catch (err) {
        res.status(200).json({ error: err.message });
    }
});

router.post('/update/email_mobile', async (req, res) => {
    try {
        payload=req.body;
        
        const userid= payload.userid;
        const email= payload.email;
        const type= payload.type;  // 1=> mobile,2=Email
        const mobile= payload.mobile;

        
        if(!userid)
        {
            response={
                'msg':"Userid Is Required",
                "status":"400"
            }
            res.status(200).send(response)
            return;
        }
        console.log(type)
        if(!type)
        {
            response={
                'msg':"Type Is Required",
                "status":"400"
            }
            res.status(200).send(response)
            return;
        }
        if(type=='2')
        {
            if(!email)
            {
                response={
                    'msg':"Email Is Required",
                    "status":"400"
                }
                res.status(200).send(response)
                return;
            }
            else
            {
                
            }
        }
        if(type=='1')
        {
            if(!mobile)
            {
                response={
                    'msg':"Mobile Is Required",
                    "status":"400"
                }
                res.status(200).send(response)
                return;
            }
            else
            {
                response2= await axios.get(`http://13.201.200.211:3000/api/send_otp/${mobile}/2`);
                console.log(response2)
                otp=response2.data.otp;
                msg=response2.data.msg;
                
                const [rows] = await req.db.query(`UPDATE users SET otp='${otp}' WHERE id='${userid}'`);
                
                response={
                    'otp':otp.toString(),
                    'msg':msg,
                    'status':"200"
                }
                res.status(200).send(response)
                return;
            }
        }
        
    } catch (err) {
        res.status(200).json({ error: err.message });
    }
});

router.post('/updateProfileImage', async (req, res) => {
    try {
      const { image, userid } = req.body;
  
      if (!image || !userid) {
        return res.status(200).json({
          success: false,
          message: 'Invalid request. Both image and userid are required.'
        });
      }
  
   
      const imageBuffer = Buffer.from(image, 'base64');
  
    
      const imageName = `${Math.floor(Math.random() * 900000) + 100000}.png`;
  
     
      const imagePath = `./public/uploads/profile/${imageName}`;
      fs.writeFileSync(imagePath, imageBuffer);
  
     
      const imageUrl = `https://yoyo11.in/uploads/profile/${imageName}`;
  
      
      const row2=await update_profile_image(req,userid,imageUrl);
  
      return res.status(200).json({
        status: "200",
        msg: 'Profile image updated successfully'
      });
    } catch (error) {
      console.error(error);
      return res.status(200).json({
        success: false,
        message: 'Internal Server Error'
      });
    }
  });
  
router.post('/user/doc_verify', async (req, res) => {
    try {
        
        payload=req.body;
        userid=payload.userid;
        type=payload.type;
        doc_number=payload.doc_number;
        
        if(!userid)
        {
            response={
                'msg':"Userid Is Required",
                "status":"400"
            }
            res.status(200).json(response)
            return;
        }
        console.log(type)
        if(!type)
        {
            response={
                'msg':"Type Is Required",
                "status":"400"
            }
            res.status(200).json(response)
            return;
        }
        if(!doc_number)
        {
            response={
                'msg':"Doc Number Is Required",
                "status":"400"
            }
            res.status(200).send(response)
            return;
        }
        if(type=='1') // Aadhar
        {
            front_image=payload.front_image;
            back_image=payload.back_image;
            if(!front_image)
            {
                response={
                    'msg':"Front Image Is Required",
                    "status":"400"
                }
                res.status(200).send(response)
                return;
            }
            if(!back_image)
            {
                response={
                    'msg':"Back Image Is Required",
                    "status":"400"
                }
                res.status(200).send(response)
                return;
            }
            
            const front_imageBuffer = Buffer.from(front_image, 'base64');
  
    
            const front_images = `${Math.floor(Math.random() * 900000) + 100000}.png`;
            
            const frontimagePath = `./public/uploads/docs/aadhar/${front_images}`;
            fs.writeFileSync(frontimagePath, front_imageBuffer);
      
         
            const front_imageUrl = `https://yoyo11.in/uploads/docs/aadhar/${front_images}`;
            // ==============================================================================================
            
            const back_imageBuffer = Buffer.from(back_image, 'base64');
  
    
            const back_images = `${Math.floor(Math.random() * 900000) + 100000}.png`;
            
            const backimagePath = `./public/uploads/docs/aadhar/${back_images}`;
            fs.writeFileSync(backimagePath, back_imageBuffer);
      
         
            const back_imageUrl = `https://yoyo11.in/uploads/docs/aadhar/${back_images}`;
            
            const docs = {
                
                    images: [front_imageUrl, back_imageUrl]
                
            };
            
            // Convert the object to a JSON string
            const doc_json = JSON.stringify(docs);
            const row2=await update_users_dac(req,userid,type,doc_number,doc_json);
        }
        if(type=='2') // DL
        {
            dl_image=payload.dl_image;
            if(!dl_image)
            {
                response={
                    'msg':"Driving License Image Is Required",
                    "status":"400"
                }
                res.status(200).send(response)
                return;
            }
            const dl_imageBuffer = Buffer.from(dl_image, 'base64');
  
    
            const dl_images = `${Math.floor(Math.random() * 900000) + 100000}.png`;
            
            const dl_imagePath = `./public/uploads/docs/dl/${dl_images}`;
            fs.writeFileSync(dl_imagePath, dl_imageBuffer);
      
         
            const dl_imageUrl = `https://yoyo11.in/uploads/docs/dl/${dl_images}`;
            
            const docs = {
                
                    images: [dl_imageUrl]
                
            };
            
            // Convert the object to a JSON string
            const doc_json = JSON.stringify(docs);
            console.log(doc_json);
            const row2=await update_users_dac(req,userid,type,doc_number,doc_json);
        }
        if(type=='3') // Voter
        {
            voter_image=payload.voter_image;
            if(!voter_image)
            {
                response={
                    'msg':"Voter Id Image Is Required",
                    "status":"400"
                }
                res.status(200).send(response)
                return;
            }
            const voter_imageBuffer = Buffer.from(voter_image, 'base64');
  
    
            const voter_images = `${Math.floor(Math.random() * 900000) + 100000}.png`;
            
            const voter_imagePath = `./public/uploads/docs/voter_idvoter_id/${voter_images}`;
            fs.writeFileSync(voter_imagePath, voter_imageBuffer);
      
         
            const voter_imageUrl = `https://yoyo11.in/uploads/docs/voter_id/${voter_images}`;
            
            const docs = {
                
                    images: [voter_imageUrl]
                
            };
            
            // Convert the object to a JSON string
            const doc_json = JSON.stringify(docs);
            const row2=await update_users_dac(req,userid,type,doc_number,doc_json);
        }
        
        
        
        response={
            'msg':"Document Upload successfully Waiting for Validation",
            "status":"200"
        }
        res.status(200).send(response)
        return;
    } catch (error) {
      console.error(error);
      return res.status(200).json({
        success: false,
        message: 'Internal Server Error'
      });
    }
  });
  
// router.post('/user/add_wallet', async (req, res) => {
//     try {
//         payload=req.body;
        
//         const userid= payload.userid;
//         const amount= payload.amount

        
//         if(!userid)
//         {
//             response={
//                 'msg':"Userid Is Required",
//                 "status":"400"
//             }
//             res.status(200).send(response)
//             return;
//         }
//         console.log(amount)
//         if(!amount)
//         {
//             response={
//                 'msg':"Amount Is Required",
//                 "status":"400"
//             }
//             res.status(200).send(response)
//             return;
//         }
//         const row2=await min_amount(req);
//         const minamount=row2[0].value;
//         console.log(minamount);
//         if(amount<parseInt(minamount))
//         {
//             response={
//                 'msg':`Minimum Limit is ₹ ${minamount}`,
//                 "status":"400"
//             }
//             res.status(200).send(response)
//             return;
//         }
        
//         const row34=await add_wallet(req,userid,amount);
        
//         response={
//             'msg':`Amount Added Successfully`,
//             "status":"200"
//         }
//         res.status(200).send(response)
//         return;
//     } catch (err) {
//         res.status(200).json({ error: err.message });
//     }
// });

router.post('/user/add_wallet', async (req, res) => {
    try {
        payload=req.body;
        const userid=payload.userid;
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
        
        const rows22 = await get_profile(req, userid);
        if(rows22.length=='0')
        {
            response={
                'msg':`Incorrect Userid`,
                "status":"400"
            }
            res.status(200).send(response)
            return;
        }
        else{
            name=rows22[0].name;
            email=rows22[0].email;
            mobile=rows22[0].mobile;
        }
        
        const transactionId = Math.floor(Math.random() * (55555555 - 11111111 + 1)) + 11111111;
        console.log(transactionId);
        // Define the payload
        const data = {
          merchantid: 'INDIANPAY00INDIANPAY0068',
          orderid: transactionId.toString(),
          amount: amount.toString(),
          name: name.toString(),
          email: "admin@gmail.com",
          mobile: mobile.toString(),
          remark: 'gg',
          type: '2',
          redirect_url: `https://khiladi11.live/api/payin_redirect?userid=${userid}&orderid=${transactionId}`
        };
        
        // Send the POST request
        axios.post('https://indianpay.co.in/admin/paynow', data, {
          headers: {
            'Content-Type': 'application/json'
          }
        })
        .then(async response => {
          console.log(response.data);
          response2=response.data;
          const resp_orderid=response2.order_id;
          const gateway_txn=response2.gateway_txn;
          const [rows2] = await req.db.query(`INSERT INTO transactions(userid, amount, type, sub_type, status,order_id,gateway_txn,transaction_id) VALUES ('${userid}','${amount}','1','1','0','${resp_orderid}','${gateway_txn}','${transactionId}')`);
          
            response={
                'data':response2,
                status:"200"
                
            }
            res.status(200).send(response)
        })
        .catch(error => {
          console.error('Error making request:', error);
        });
    } catch (err) {
        res.status(200).json({ error: err.message });
    }
});

router.get('/payin_redirect', async (req, res) => {
    try {
        payload=req.query;
        const userid=payload.userid;
        const orderid=payload.orderid;
        
        if(!userid)
        {
            response={
                'msg':"Userid Is Required",
                "status":"400"
            }
            res.status(200).send(response)
            return;
        }
        
        let config = {
            method: 'get',
            maxBodyLength: Infinity,
            url: `https://indianpay.co.in/admin/payinstatus?order_id=${orderid}`,
            headers: { 
                'Cookie': 'ci_session=5kb8klme163mk6dn7v73loojce2qiitd'
            }
        };
        
        axios.request(config)
        .then(async (response) => {
            console.log(JSON.stringify(response.data));
            response2=response.data;
          
            paymentstatus=response2.status;
            amount=response2.amount;
            if(paymentstatus=='success')
            {
                paymentstatus2='1';
                apistatus="200";
                
                const row34=await add_wallet(req,userid,amount);
            }
            if(paymentstatus=='reject')
            {
                paymentstatus2='2';
                apistatus="400";
            }
            
            transactionId=response2.transactionid;
            utr=response2.utr;
            
            const rows22 = await update_payin_status(req, userid,transactionId,utr,paymentstatus2);
            response={
                "transactionid":transactionId,
                "utr":utr,
                "paymentstatus":paymentstatus,
                "status":apistatus,
                'msg':`Amount Added Successfully`,
            }
            res.status(200).send(response)
            return;
        })
        .catch((error) => {
          console.log(error);
        });
        

    } catch (err) {
        res.status(200).json({ error: err.message });
    }
});

router.post('/user/payout', async (req, res) => {
    try {
        payload=req.body;
        const userid=payload.userid;
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
        
        const rows22 = await get_profile(req, userid);
        if(rows22.length=='0')
        {
            response={
                'msg':`Incorrect Userid`,
                "status":"400"
            }
            res.status(200).send(response)
            return;
        }
        else{
            name=rows22[0].name;
            email=rows22[0].email;
            mobile=rows22[0].mobile;
        }
        
        const transactionId = Math.floor(Math.random() * (55555555 - 11111111 + 1)) + 11111111;
        console.log(transactionId);
        // Define the payload
        let data = JSON.stringify({
        "merchant_id":"INDIANPAY00INDIANPAY0068",
        "merchant_token":"2PuSwrcosiilsd3YdgyD2Qew4dTBtyqJ",
        "account_no":"6319094757",
        "ifsccode":"IDIB000K236",
        "amount":amount.toString(),
        "bankname":"indianbank",
        "remark":"remark",
        "orderid":transactionId.toString(),
        "name":"Aneeta Jaiswal",
        "contact":"7081472797",
        "email":"anurag@gmail.com"
        });
        console.log(data);
        let encodedData = Buffer.from(data).toString('base64');

        console.log(encodedData);
            
        let config = {
          salt : encodedData
        };
        
        console.log(config);
        
        axios.post('https://indianpay.co.in/admin/single_transaction', config, {
          headers: {
            'Content-Type': 'application/json'
          }
        })
        .then((response) => {
          console.log(JSON.stringify(response.data));
          
            response2=response.data;
            response={
                'data':response2,
                status:"200"
                
            }
            res.status(200).send(response)
        })
        .catch((error) => {
          console.log(error);
        });
    } catch (err) {
        res.status(200).json({ error: err.message });
    }
});

router.get('/get/transactions_type', async (req, res) => {
  try {
    const rows = await get_transaction_type(req);
    if(rows.length=='0')
    {
        data=[];
        msg="No Data Found";
        apistatus=400;
    }
    else
    {
        data=rows;
        msg="Success";
        apistatus=200;
    }
    response={
        'data':data,
        'msg': msg,
        'status':apistatus.toString()
    }
    res.status(200).send(response)
    return;
  } catch (err) {
    res.status(200).json({ error: err.message });
  }
});

router.get('/get/transactions/:userid', async (req, res) => {
  try {
      userid=req.params.userid;
    const rows = await get_transaction(req,userid);
    if(rows.length=='0')
    {
        data=[];
        msg="No Data Found";
        apistatus=400;
    }
    else
    {
        data=rows;
        msg="Success";
        apistatus=200;
    }
    response={
        'data':data,
        'msg': msg,
        'status':apistatus.toString()
    }
    res.status(200).send(response)
    return;
  } catch (err) {
    res.status(200).json({ error: err.message });
  }
});

router.get('/get/games', async (req, res) => {
  try {
    const rows = await get_games(req);
    if(rows.length=='0')
    {
        data=[];
        msg="No Data Found";
        apistatus=400;
    }
    else
    {
        data=rows;
        msg="Success";
        apistatus=200;
    }
    response={
        'data':data,
        'msg': msg,
        'status':apistatus.toString()
    }
    res.status(200).send(response)
    return;
  } catch (err) {
    res.status(200).json({ error: err.message });
  }
});

router.get('/get/match_status', async (req, res) => {
  try {
    const rows = await match_status(req);
    if(rows.length=='0')
    {
        data=[];
        msg="No Data Found";
        apistatus=400;
    }
    else
    {
        data=rows;
        msg="Success";
        apistatus=200;
    }
    response={
        'data':data,
        'msg': msg,
        'status':apistatus.toString()
    }
    res.status(200).send(response)
    return;
  } catch (err) {
    res.status(200).json({ error: err.message });
  }
});

router.get('/my/matches/:userid/:gameid', async (req, res) => {
    try {
        userid= req.params.userid;
        gameid=req.params.gameid;
        const upcoming_matche = await upcoming_matches(req,gameid);
        if(upcoming_matche.length=='0')
        {
            upcoming_match=[];
        }
        else
        {
            upcoming_match=upcoming_matche;
        }
        
        const my_live_matche = await my_live_matches(req,userid,gameid);
        if(my_live_matche.length=='0')
        {
            live_match=[];
        }
        else
        {
            live_match=my_live_matche;
        }
        
        const my_complete_matche = await my_complete_matches(req,userid,gameid);
        if(my_complete_matche.length=='0')
        {
            complete_match=[];
        }
        else
        {
            complete_match=my_complete_matche;
        }
        
        const my_upcoming_matche = await my_upcoming_matches(req,userid,gameid);
        if(my_upcoming_matche.length=='0')
        {
            my_upcoming_match=[];
        }
        else
        {
            my_upcoming_match=my_upcoming_matche;
        }
        response={
            'upcoming':upcoming_match,
            'live':live_match,
            'complete':complete_match,
            'my_upcoming_match':my_upcoming_match,
            'msg': "Success",
            'status':"200"
        }
        res.status(200).send(response)
        return;
    } 
    catch (err) {
        res.status(200).json({ error: err.message });
    }
});

router.post('/match_details/', async (req, res) => {
    try {
        payload=req.body;
        matchid= payload.matchid;
        userid= payload.userid;
        type= payload.type;
        gameid= payload.gameid;
        
        if(!userid)
        {
            response={
                'msg':"User Id Is Required",
                "status":"400"
            }
            res.status(200).send(response)
            return;
        }
        if(!gameid)
        {
            response={
                'msg':"Game Id Is Required",
                "status":"400"
            }
            res.status(200).send(response)
            return;
        }
        if(!matchid)
        {
            response={
                'msg':"Match Id Is Required",
                "status":"400"
            }
            res.status(200).send(response)
            return;
        }
        if(!type)
        {
            response={
                'msg':"Type Is Required",
                "status":"400"
            }
            res.status(200).send(response)
            return;
        }
        
        if(type=='1')  // Upcomming
        {
            const contestlist = await contest_list(req,matchid,gameid);
            if(contestlist.length=='0')
            {
                contestlists=[];
            }
            else
            {
                contestlists=contestlist;
            }
            
            const mycontest = await mycontest_list(req,userid,gameid,matchid);
            if(mycontest.length=='0')
            {
                mycontests=[];
            }
            else
            {
                mycontests=mycontest;
            }
            
            const match_details = await match_details_byid(req,matchid);
            if(match_details.length=='0')
            {
                matchName='';
            }
            else
            {
                matchName=match_details[0].name;
                series_id=match_details[0].series_id;
            }
            response={
                'matchName':matchName,
                'series_id':series_id,
                'contestlist':contestlists,
                'mycontest':mycontests,
                'msg': "Success",
                'status':"200"
            }
            res.status(200).send(response)
            return;
        }
        if(type=='2')  // Live
        {
            const mycontest = await mycontest_list(req,userid,gameid,matchid);
            if(mycontest.length=='0')
            {
                mycontests=[];
            }
            else
            {
                mycontests=mycontest;
            }
            response={
                'mycontest':mycontests,
                'msg': "Success",
                'status':"200"
            }
            res.status(200).send(response)
            return;
        }
        
    } 
    catch (err) {
        res.status(200).json({ error: err.message });
    }
});

router.get('/contest/details/:contestid/:matchid', async (req, res) => {
    try {
        contestid=req.params.contestid;
        matchid=req.params.matchid;
        
        const match_details = await match_details_byid(req,matchid);
        if(match_details.length=='0')
        {
            matchName='';
        }
        else
        {
            matchName=match_details[0].name;
            series_id=match_details[0].series_id;
        }
        const contest_winnings = await contest_winning(req,contestid);
        if(contest_winnings.length=='0')
        {
            winning=[];
            msg="No Data Found";
            apistatus=400;
        }
        else
        {
            prize_pool=contest_winnings[0].prize_pool;
            total_spot=contest_winnings[0].total_spot;
            entry_fee=contest_winnings[0].entry_fee;
            entry_limit=contest_winnings[0].entry_limit;
            contest_success_type=contest_winnings[0].contest_success_type;
            
            winning=JSON.parse(contest_winnings[0].winning_details);
            first_prize=winning[0].prize;
            msg="Success";
            apistatus=200;
        }
        response={
            'matchName':matchName,
            'series_id':series_id,
            'prize_pool':prize_pool,
            'total_spot':total_spot,
            'entry_fee':entry_fee,
            'first_prize':first_prize,
            'entry_limit':entry_limit,
            'contest_success_type':contest_success_type,
            'winning':winning,
            'leaderboard':[],
            'msg': msg,
            'status':apistatus.toString()
        }
        res.status(200).send(response)
        return;
    } catch (err) {
        res.status(200).json({ error: err.message });
    }
});

router.get('/contest/filterType', async (req, res) => {
    try {
        
        const contest_filters = await contest_filter(req);
        if(contest_filters.length=='0')
        {
            contest_data=[];
            msg="No Data Found";
            apistatus=400;
        }
        else
        {
            contest_data=contest_filters;
            msg="Success";
            apistatus=200;
        }
        response={
            'contest_filter':contest_data,
            
            'msg': msg,
            'status':apistatus.toString()
        }
        res.status(200).send(response)
        return;
    } catch (err) {
        res.status(200).json({ error: err.message });
    }
});

router.get('/get/banner', async (req, res) => {
  try {
      const type="Home";
    const rows = await get_banner(req,type);
    if(rows.length=='0')
    {
        data=[];
        msg="No Data Found";
        apistatus=400;
    }
    else
    {
        data=rows;
        msg="Success";
        apistatus=200;
    }
    response={
        'data':data,
        'msg': msg,
        'status':apistatus.toString()
    }
    res.status(200).send(response)
    return;
  } catch (err) {
    res.status(200).json({ error: err.message });
  }
});

router.get('/get/promo/banner', async (req, res) => {
  try {
      const type="Promo";
    const rows = await get_banner(req,type);
    if(rows.length=='0')
    {
        data=[];
        msg="No Data Found";
        apistatus=400;
    }
    else
    {
        data=rows;
        msg="Success";
        apistatus=200;
    }
    response={
        'data':data,
        'msg': msg,
        'status':apistatus.toString()
    }
    res.status(200).send(response)
    return;
  } catch (err) {
    res.status(200).json({ error: err.message });
  }
});

router.post('/user/withdrawal', async (req, res) => {
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
        
        const row22=await get_withdraw_amount(req,userid,amount);
        
        if(row22.length==0)
        {
            response={
                'msg':`Insufficiant Amount In Wallet`,
                "status":"400"
            }
            res.status(200).send(response)
            return;
        }
        
        // const row2=await min_withdrawal_amount(req);
        // const minamount=row2[0].value;
        // console.log(minamount);
        // if(amount<parseInt(minamount))
        // {
        //     response={
        //         'msg':`Minimum Limit is ₹ ${minamount}`,
        //         "status":"400"
        //     }
        //     res.status(200).send(response)
        //     return;
        // }
        
        
        
        const row34=await withdraw_wallet(req,userid,amount);
        
        const [rows] = await req.db.query(`INSERT INTO transactions(userid, amount, type, sub_type, status) VALUES ('${userid}','${amount}','3','3','0')`);
        
        response={
            'msg':`Amount Withdrawal Successfully`,
            "status":"200"
        }
        res.status(200).send(response)
        return;
    } catch (err) {
        res.status(200).json({ error: err.message });
    }
});

router.post('/contest/filterWiseData', async (req, res) => {
    try {
        payload=req.body;
        filtertype=payload.filtertype;
        filter_value=payload.filter_value;
        matchid= payload.matchid;
        gameid= payload.gameid;
        
        if(!gameid)
        {
            response={
                'msg':"Game Id Is Required",
                "status":"400"
            }
            res.status(200).send(response)
            return;
        }
        if(!matchid)
        {
            response={
                'msg':"Match Id Is Required",
                "status":"400"
            }
            res.status(200).send(response)
            return;
        }
        if(!filtertype)
        {
            response={
                'msg':"Filter Type Is Required",
                "status":"400"
            }
            res.status(200).send(response)
            return;
        }
        if(!filter_value)
        {
            response={
                'msg':"Filter Value Is Required",
                "status":"400"
            }
            res.status(200).send(response)
            return;
        }
        
        const contest_filters = await contestFilterWiseData(req,filtertype,filter_value,gameid,matchid);
        if(contest_filters.length=='0')
        {
            contest_data=[];
            msg="No Data Found";
            apistatus=400;
        }
        else
        {
            contest_data=contest_filters;
            msg="Success";
            apistatus=200;
        }
        response={
            'contestlist':contest_data,
            
            'msg': msg,
            'status':apistatus.toString()
        }
        res.status(200).send(response)
        return;
    } catch (err) {
        res.status(200).json({ error: err.message });
    }
});

router.get('/get/players_by_series/:seriesId', async (req, res) => {
    try {
        payload=req.params;
        seriesId=payload.seriesId;
        if(!seriesId)
        {
            response={
                'msg':"Series Id Is Required",
                "status":"400"
            }
            res.status(200).send(response)
            return;
        }
        
        const player_data = await get_players_bySeries(req,seriesId);
        if(player_data.length=='0')
        {
            data=[];
            msg="No Data Found";
            apistatus=400;
        }
        else
        {
            data=player_data;
            msg="Success";
            apistatus=200;
        }
        response={
            'data':data,
            
            'msg': msg,
            'status':apistatus.toString()
        }
        res.status(200).send(response)
        return;
    } catch (err) {
        res.status(200).json({ error: err.message });
    }
});

router.post('/my_yesterday_refereral', async (req, res) => {
    try {
        payload=req.body;
        userid=payload.userid;
        date=payload.date;
        if(!userid)
        {
            response={
                'msg':"User Id Is Required",
                "status":"400"
            }
            res.status(200).send(response)
            return;
        }
        if(!date)
        {
            response={
                'msg':"Date Is Required",
                "status":"400"
            }
            res.status(200).send(response)
            return;
        }
        const rows2 = await get_profile(req, userid);
        invitation_code=rows2[0].Invitation_code;
        
        const direct_subordinate_data = await direct_subordinate(req,userid,date);
        const team_subordinatee_data = await team_subordinate(req,userid,date);
        if(direct_subordinate_data.length=='0')
        {
            data=[];
            msg="No Data Found";
            apistatus=400;
        }
        else
        {
            msg="Success";
            apistatus=200;
        }
        response={
            'invitation_code':invitation_code,
            'yesterday_total_comission':16,
            'direct_subordinate':direct_subordinate_data[0].json_result[0],
            'team_subordinate':team_subordinatee_data[0].json_result[0],
            'promotion_data':{
                'total_commission':0,
                'direct_subordinate':10,
                'direct_total_salery':0,
                'today_salary':5,
                'team_subordinate_count':12,
                'team_total_salary':15
            },
            'msg': msg,
            'status':apistatus.toString()
        }
        res.status(200).send(response)
        return;
    } catch (err) {
        res.status(200).json({ error: err.message });
    }
});

router.post('/my_refereral', async (req, res) => {
    try {
        payload=req.body;
        userid=payload.userid;
        type=payload.type;  // 1=>Today,2=>Yesterday,3=>This Month
        if(!userid)
        {
            response={
                'msg':"User Id Is Required",
                "status":"400"
            }
            res.status(200).send(response)
            return;
        }
        if(!type)
        {
            response={
                'msg':"Type Is Required",
                "status":"400"
            }
            res.status(200).send(response)
            return;
        }
        const rows2 = await my_refreals(req, userid,type);
        if(rows2.length=='0')
        {
            data=[];
            msg="No Data Found";
            apistatus=400;
        }
        else
        {
            data=rows2;
            msg="Success";
            apistatus=200;
        }
        response={
            'data':data,
            'msg': msg,
            'status':apistatus.toString()
        }
        res.status(200).send(response)
        return;
    } catch (err) {
        res.status(200).json({ error: err.message });
    }
});

router.post('/my_tear_wise_subordinatedata', async (req, res) => {
    try {
        payload=req.body;
        userid=payload.userid;
        date=payload.date;
        tear=payload.tear;
        if(!userid)
        {
            response={
                'msg':"User Id Is Required",
                "status":"400"
            }
            res.status(200).send(response)
            return;
        }
        
        if(!tear)
        {
            response={
                'msg':"Tear Is Required",
                "status":"400"
            }
            res.status(200).send(response)
            return;
        }
        const rows2 = await my_tear_wise_subordinatedata(req, userid,date,tear);
        if(rows2.length=='0')
        {
            data=[];
            msg="No Data Found";
            apistatus=400;
        }
        else
        {
            data=rows2;
            msg="Success";
            apistatus=200;
        }
        response={
            'data':data,
            'msg': msg,
            'status':apistatus.toString()
        }
        res.status(200).send(response)
        return;
    } catch (err) {
        res.status(200).json({ error: err.message });
    }
});

router.get('/get/settings/:type', async (req, res) => {
    try {
        payload=req.params;
        type=payload.type;
        if(!type)
        {
            response={
                'msg':"Type Is Required",
                "status":"400"
            }
            res.status(200).send(response)
            return;
        }
        
        if(type=='1') //Privacy Policy
        {
            name="Privacy Policy"
            
            console.log(name)
            settings = await get_settings(req,name);
        }
        if(type=='2') //About Us
        {
            name="About Us"
            
            settings = await get_settings(req,name);
        }
        if(type=='3') //Terms Condition
        {
            name="Terms Condition"
            
            settings = await get_settings(req,name);
        }
        
        
        if(settings.length=='0')
        {
            data=[];
            msg="No Data Found";
            apistatus=400;
        }
        else
        {
            datas=settings[0].value;
            headings=settings[0].name;
            
            data=datas
            msg="Success";
            apistatus=200;
        }
        response={
            'headings':headings,
            'data':data,
            
            'msg': msg,
            'status':apistatus.toString()
        }
        res.status(200).send(response)
        return;
    } catch (err) {
        res.status(200).json({ error: err.message });
    }
});

router.get('/get/how_to_play/:gameid', async (req, res) => {
    try {
        payload=req.params;
        
        gameid=payload.gameid;
        if(!gameid)
        {
            response={
                'msg':"Game Id Is Required",
                "status":"400"
            }
            res.status(200).send(response)
            return;
        }
        how_to_plays = await how_to_play(req,gameid);
        
        
        if(how_to_plays.length=='0')
        {
            data=[];
            msg="No Data Found";
            apistatus=400;
        }
        else
        {
            
            data=how_to_plays;
            msg="Success";
            apistatus=200;
        }
        response={
            'data':data,
            
            'msg': msg,
            'status':apistatus.toString()
        }
        res.status(200).send(response)
        return;
    } catch (err) {
        res.status(200).json({ error: err.message });
    }
});

router.get('/get/notification/:userid/:type', async (req, res) => {
    try {
        payload=req.params;
        
        type=payload.type;
        userid=payload.userid;
        if(!userid)
        {
            response={
                'msg':"Userid Is Required",
                "status":"400"
            }
            res.status(200).send(response)
            return;
        }
        if(!type)
        {
            response={
                'msg':"Type Is Required",
                "status":"400"
            }
            res.status(200).send(response)
            return;
        }
        notification = await get_notifications(req,type,userid,"0");
        
        all_notification = await get_notifications(req,type,userid,"3");
        
        if(notification.length=='0')
        {
            data=[];
            msg="No Data Found";
            apistatus=400;
        }
        else
        {
            
            data=notification;
            msg="Success";
            apistatus=200;
        }
        response={
            'counts':all_notification.length.toString(),
            'data':notification,
            'msg': msg,
            'status':apistatus.toString()
        }
        res.status(200).send(response)
        return;
    } catch (err) {
        res.status(200).json({ error: err.message });
    }
});

router.post('/save/viewed/notification', async (req, res) => {
    try {
        payload=req.body;
        
        notification_id=payload.notification_id;
        userid=payload.userid;
        if(!userid)
        {
            response={
                'msg':"Userid Is Required",
                "status":"400"
            }
            res.status(200).send(response)
            return;
        }
        if(!notification_id)
        {
            response={
                'msg':"Notification Id Is Required",
                "status":"400"
            }
            res.status(200).send(response)
            return;
        }
        notification = await save_viewed_notifications(req,notification_id,userid);
        
        
        response={
            'msg': "Success",
            'status':"200"
        }
        res.status(200).send(response)
        return;
    } catch (err) {
        res.status(200).json({ error: err.message });
    }
});

    
// Add more API routes as needed

module.exports = router


