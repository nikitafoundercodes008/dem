async function getsettings(req) {
  try {
    const [rows] = await req.db.query(`SELECT * FROM settings WHERE status='1'`);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function admin_login(req,email,password) {
  try {
      query=`SELECT * FROM  admins  WHERE  email ='${email}' &&  password='${password}' && status ='1'`;
      console.log(query)
    const [rows] = await req.db.query(query);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function dynamic_dashboard(req,roleid) {
    
    try {
        var1=`SELECT * FROM role_permission WHERE role='${roleid}' && module='dashboard'`;
        console.log(var1)
        const [rows_2] = await req.db.query(var1);
        console.log(rows_2)
        
        var permission_id = rows_2[0]['permission_id'];
        console.log(permission_id);
        const stringValue= `(${permission_id})`
        console.log(stringValue);
        
        query2=`SELECT *,(SELECT name FROM color ORDER BY RAND() * (SELECT MAX(id) FROM color) LIMIT 1) As colorname,(count_module) AS counts FROM permission WHERE id IN ${stringValue}`;
        console.log(query2)
        const [rows] = await req.db.query(query2);
        return rows;
    } catch (err) {
        throw new Error(err.message);
    }
}

async function dynamic_sidebar(req,roleid) {
    
    try {
        var1=`SELECT * FROM role_permission WHERE role='${roleid}' && module='sidebar'`;
        console.log(var1)
        const [rows_2] = await req.db.query(var1);
        console.log(rows_2)
        
        var permission_id = rows_2[0]['permission_id'];
        console.log(permission_id);
        const stringValue= `(${permission_id})`
        console.log(stringValue);
        
        query2=`SELECT *,(SELECT name FROM color ORDER BY RAND() * (SELECT MAX(id) FROM color) LIMIT 1) As colorname FROM permission WHERE id IN ${stringValue} ORDER BY orders ASC`;
        console.log(query2)
        const [rows] = await req.db.query(query2);
        return rows;
    } catch (err) {
        throw new Error(err.message);
    }
}

async function update_business_setup(req,id,value) {
  try {
      query=`UPDATE settings SET value='${value}' WHERE id='${id}'`;
      console.log(query)
    const [rows] = await req.db.query(query);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function get_games(req) {
  try {
    const [rows] = await req.db.query(`SELECT *,CASE WHEN status='1' THEN 'Active' ELSE 'Inactive' END AS active_status FROM games WHERE status='1'`);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function match_details(req,gameid,type) {
    try {
        if(type=='1')
        {
            const [rows] = await req.db.query(`SELECT matches.*,series.name AS seriesname,match_type.slug AS matchtype,
                JSON_UNQUOTE(JSON_EXTRACT(matches.team_id, '$[0]')) AS home_teamid,
                JSON_UNQUOTE(JSON_EXTRACT(matches.team_id, '$[1]')) AS visitorteam_id,
                t1.name AS hometeam_name,t2.name AS visitorteam_name,t1.image AS hometeam_image,t2.image AS visitorteam_image,t1.slug AS hometeam_short_name,t2.slug AS visitorteam_short_name FROM matches 
                LEFT JOIN series ON series.id=matches.series_id
                LEFT JOIN match_type ON match_type.id=matches.matchtype_id
                LEFT JOIN teams t1 ON JSON_UNQUOTE(JSON_EXTRACT(matches.team_id, '$[0]')) = t1.id
                LEFT JOIN teams t2 ON JSON_UNQUOTE(JSON_EXTRACT(matches.team_id, '$[1]')) = t2.id
                WHERE matches.game_id=${gameid} AND matches.start_date > NOW() AND matches.status=1`);
                console.log(rows)
            return rows;
        } 
        if(type=='2')
        {
            sql=`SELECT matches.*,series.name AS seriesname,match_type.slug AS matchtype,
                JSON_UNQUOTE(JSON_EXTRACT(matches.team_id, '$[0]')) AS home_teamid,
                JSON_UNQUOTE(JSON_EXTRACT(matches.team_id, '$[1]')) AS visitorteam_id,
                t1.name AS hometeam_name,t2.name AS visitorteam_name,t1.image AS hometeam_image,t2.image AS visitorteam_image,t1.slug AS hometeam_short_name,t2.slug AS visitorteam_short_name  FROM matches 
                LEFT JOIN series ON series.id=matches.series_id
                LEFT JOIN match_type ON match_type.id=matches.matchtype_id
                LEFT JOIN teams t1 ON JSON_UNQUOTE(JSON_EXTRACT(matches.team_id, '$[0]')) = t1.id
                LEFT JOIN teams t2 ON JSON_UNQUOTE(JSON_EXTRACT(matches.team_id, '$[1]')) = t2.id
                WHERE matches.game_id=${gameid} AND matches.start_date < NOW() AND matches.status=2`;
                console.log(sql)
            const [rows] = await req.db.query(sql);
                
            return rows;
        } 
        if(type=='3')
        {
            sql=`SELECT matches.*,series.name AS seriesname,match_type.slug AS matchtype,
                JSON_UNQUOTE(JSON_EXTRACT(matches.team_id, '$[0]')) AS home_teamid,
                JSON_UNQUOTE(JSON_EXTRACT(matches.team_id, '$[1]')) AS visitorteam_id,
                t1.name AS hometeam_name,t2.name AS visitorteam_name,t1.image AS hometeam_image,t2.image AS visitorteam_image,t1.slug AS hometeam_short_name,t2.slug AS visitorteam_short_name  FROM matches 
                LEFT JOIN series ON series.id=matches.series_id
                LEFT JOIN match_type ON match_type.id=matches.matchtype_id
                LEFT JOIN teams t1 ON JSON_UNQUOTE(JSON_EXTRACT(matches.team_id, '$[0]')) = t1.id
                LEFT JOIN teams t2 ON JSON_UNQUOTE(JSON_EXTRACT(matches.team_id, '$[1]')) = t2.id
                WHERE matches.game_id=${gameid} AND matches.start_date < NOW() AND matches.status=3`;
                 console.log(sql)
            const [rows] = await req.db.query(sql);
                console.log(rows)
            return rows;
        } 
        
    }
    catch (err) {
        throw new Error(err.message);
    }
}

async function contest_details(req,matchid) {
  try {
    const [rows] = await req.db.query(`SELECT contests.*,contest_details.prize_pool,contest_details.entry_fee,contest_details.total_spot,
    CASE
        WHEN contest_details.success_type='1' THEN 'Guranteed'
        WHEN contest_details.success_type='2' THEN 'Flexible'
        ELSE ''
    END AS contest_success_type FROM contests LEFT JOIN contest_details ON contest_details.contest_id=contests.id WHERE (contests.match_id='${matchid}' OR contests.match_id IS NULL)`);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function get_teams(req) {
  try {
    const [rows] = await req.db.query(`SELECT *,CASE WHEN status='1' THEN 'Active' ELSE 'Inactive' END AS active_status FROM teams WHERE status='1'`);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function get_designation(req,gameid) {
  try {
    const [rows] = await req.db.query(`SELECT * FROM designation WHERE gameid='${gameid}'`);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function get_all_users(req) {
  try {
      sql=`SELECT users.*,CASE WHEN status='1' THEN 'Active' ELSE 'Inactive' END AS active_status,CASE WHEN is_verify='1' THEN 'Verified' ELSE 'Un Verfied' END AS verify_status,CASE WHEN gender='1' THEN 'Male' WHEN gender='2' THEN 'Female' WHEN gender='3' THEN 'Others' ELSE '' END AS genders,CASE WHEN (image IS NULL AND gender='1') THEN '/assets/images/user.png' WHEN (image IS NULL AND gender='2') THEN 'https://static.vecteezy.com/system/resources/thumbnails/004/899/680/small/beautiful-blonde-woman-with-makeup-avatar-for-a-beauty-salon-illustration-in-the-cartoon-style-vector.jpg' WHEN (image IS NULL AND gender='0') THEN '/assets/images/user.png' ELSE image END AS images,user_details.winning_wallet,user_details.wallet,user_details.unutiliesed_wallet,user_details.bonus_wallet FROM users LEFT JOIN user_details ON user_details.user_id=users.id`;
      console.log(sql);
    const [rows] = await req.db.query(sql);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function add_wallet(req,userid,amount) {
  try {
      sql=`UPDATE user_details SET wallet=wallet+${amount},unutiliesed_wallet=unutiliesed_wallet+${amount} WHERE user_id='${userid}'`;
      console.log(sql)
        const [rows] = await req.db.query(sql);
        
        const [rows2] = await req.db.query(`INSERT INTO transactions(userid, amount, type, sub_type, status,created_at, updated_at) VALUES ('${userid}','${amount}','1','1','1','','')`);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function min_amount(req) {
  try {
    const [rows] = await req.db.query(`SELECT value FROM settings WHERE name='Min Amount' && status='1'`);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function users_doc(req) {
  try {
      sql=`SELECT users.*,doc_verification.type AS doctype,doc_verification.document_number,doc_verification.doc_image,doc_verification.status,CASE WHEN doc_verification.status='1' THEN 'Verfied' WHEN doc_verification.status='2' THEN 'Rejected' ELSE 'Un Verified' END AS verify_status,CASE WHEN doc_verification.type='1' THEN 'Aadhar' WHEN doc_verification.type='2' THEN 'DL' WHEN doc_verification.type='3' THEN 'Voter ID' ELSE '' END AS types FROM doc_verification LEFT JOIN users ON doc_verification.userid=users.id`;
      console.log(sql);
    const [rows] = await req.db.query(sql);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function doc_byid(req,id) {
  try {
      sql=`SELECT users.*,doc_verification.type AS doctype,doc_verification.document_number,doc_verification.doc_image,doc_verification.status,doc_verification.id AS docid,CASE WHEN doc_verification.status='1' THEN 'Verfied' WHEN doc_verification.status='2' THEN 'Rejected' ELSE 'Un Verified' END AS verify_status,CASE WHEN doc_verification.type='1' THEN 'Aadhar' WHEN doc_verification.type='2' THEN 'DL' WHEN doc_verification.type='3' THEN 'Voter ID' ELSE '' END AS types FROM doc_verification LEFT JOIN users ON doc_verification.userid=users.id WHERE doc_verification.userid='${id}'`;
      console.log(sql);
    const [rows] = await req.db.query(sql);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function active(req,tablename,id){
  try{
    var sql = `UPDATE ${tablename} SET status='1' WHERE id='${id}'`;
    console.log(sql);
    const [rows] = await req.db.query(sql);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function deactive(req,tablename,id){
  try{
    var sql = `UPDATE ${tablename} SET status='2' WHERE id='${id}'`;
    console.log(sql);
    const [rows] = await req.db.query(sql);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function deletes(req,tablename,id){
  try{
    var sql = `DELETE FROM ${tablename} WHERE id='${id}'`;
    console.log(sql);
    const [rows] = await req.db.query(sql);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function get_withdrawal_request(req,type){
  try{
    var sql = `SELECT transactions.*,users.name AS name,users.username,users.mobile AS mobile FROM transactions LEFT JOIN users ON users.id=transactions.userid WHERE transactions.type=(SELECT id FROM transaction_type WHERE name='Withdrawal') AND transactions.status='${type}' AND transactions.is_destroyed='0'`;
    console.log(sql);
    const [rows] = await req.db.query(sql);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function reject_withdrawal_request(req,transactionid,amount,userid){
  try{
    var sql2 = `UPDATE transactions SET status='2',is_destroyed='1' WHERE id='${transactionid}'`;
    const [rows3] = await req.db.query(sql2);
    
    var sql = `INSERT INTO transactions(userid,amount,status,type,sub_type) VALUES ('${userid}','${amount}','2',(SELECT id FROM transaction_type WHERE name='Withdrawal'),(SELECT id FROM transaction_type WHERE name='Refuded'))`;
    console.log(sql);
    const [rows] = await req.db.query(sql);
    
     sql2=`UPDATE user_details SET wallet=wallet+${amount},winning_wallet=winning_wallet+${amount} WHERE user_id='${userid}'`;
      console.log(sql2)
        const [rows2] = await req.db.query(sql2);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function get_transactionbyid(req,transactionid){
  try{
    var sql = `SELECT transactions.* FROM transactions WHERE id='${transactionid}'`;
    console.log(sql);
    const [rows] = await req.db.query(sql);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function get_banner(req,type) {
  try {
    const [rows] = await req.db.query(`SELECT *,CASE WHEN status='1' THEN 'Active' ELSE 'Inactive' END AS active_status FROM banners WHERE type='${type}'`);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

module.exports = {
  getsettings,admin_login,dynamic_dashboard,dynamic_sidebar,update_business_setup,get_games,match_details,contest_details,get_teams,get_designation,get_all_users,add_wallet,min_amount,users_doc,doc_byid,active,deactive,get_withdrawal_request,reject_withdrawal_request,get_transactionbyid,get_banner,deletes
};