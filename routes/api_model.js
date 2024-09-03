const moment = require('moment');

function generateRandomString(length) {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let result = '';
    for (let i = 0; i < length; i++) {
        result += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return result;
}

async function my_upcoming_matches(req, userid,gameid) {
  try {
    const [rows] = await req.db.query(`SELECT matches.*,series.name AS seriesname,match_type.slug AS matchtype,
JSON_UNQUOTE(JSON_EXTRACT(matches.team_id, '$[0]')) AS home_teamid,
JSON_UNQUOTE(JSON_EXTRACT(matches.team_id, '$[1]')) AS visitorteam_id,
t1.name AS hometeam_name,t2.name AS visitorteam_name,t1.image AS hometeam_image,t2.image AS visitorteam_image,t1.slug AS hometeam_short_name,t2.slug AS visitorteam_short_name FROM my_matches 
LEFT JOIN matches ON matches.id=my_matches.match_id
LEFT JOIN series ON series.id=matches.series_id
LEFT JOIN match_type ON match_type.id=matches.matchtype_id
LEFT JOIN teams t1 ON JSON_UNQUOTE(JSON_EXTRACT(matches.team_id, '$[0]')) = t1.id
LEFT JOIN teams t2 ON JSON_UNQUOTE(JSON_EXTRACT(matches.team_id, '$[1]')) = t2.id
WHERE my_matches.userid=${userid} AND matches.game_id=${gameid} AND matches.start_date > NOW() AND matches.status=1`);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function upcoming_matches(req, gameid) {
  try {
    const [rows] = await req.db.query(`SELECT matches.*,series.name AS seriesname,match_type.slug AS matchtype,
JSON_UNQUOTE(JSON_EXTRACT(matches.team_id, '$[0]')) AS home_teamid,
JSON_UNQUOTE(JSON_EXTRACT(matches.team_id, '$[1]')) AS visitorteam_id,
t1.name AS hometeam_name,t2.name AS visitorteam_name,t1.image AS hometeam_image,t2.image AS visitorteam_image,t1.slug AS hometeam_short_name,t2.slug AS visitorteam_short_name FROM matches 
LEFT JOIN series ON series.id=matches.series_id
LEFT JOIN match_type ON match_type.id=matches.matchtype_id
LEFT JOIN teams t1 ON JSON_UNQUOTE(JSON_EXTRACT(matches.team_id, '$[0]')) = t1.id
LEFT JOIN teams t2 ON JSON_UNQUOTE(JSON_EXTRACT(matches.team_id, '$[1]')) = t2.id
WHERE matches.game_id=${gameid} AND matches.start_date > NOW() AND matches.status=1`);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function my_live_matches(req, userid,gameid) {
  try {
    const [rows] = await req.db.query(`SELECT matches.*,series.name AS seriesname,match_type.slug AS matchtype,
JSON_UNQUOTE(JSON_EXTRACT(matches.team_id, '$[0]')) AS home_teamid,
JSON_UNQUOTE(JSON_EXTRACT(matches.team_id, '$[1]')) AS visitorteam_id,
t1.name AS hometeam_name,t2.name AS visitorteam_name,t1.image AS hometeam_image,t2.image AS visitorteam_image,t1.slug AS hometeam_short_name,t2.slug AS visitorteam_short_name  FROM my_matches 
LEFT JOIN matches ON matches.id=my_matches.match_id
LEFT JOIN series ON series.id=matches.series_id
LEFT JOIN match_type ON match_type.id=matches.matchtype_id
LEFT JOIN teams t1 ON JSON_UNQUOTE(JSON_EXTRACT(matches.team_id, '$[0]')) = t1.id
LEFT JOIN teams t2 ON JSON_UNQUOTE(JSON_EXTRACT(matches.team_id, '$[1]')) = t2.id
WHERE my_matches.userid=${userid} AND matches.game_id=${gameid} AND matches.start_date < NOW() AND matches.status=2`);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function my_complete_matches(req, userid,gameid) {
  try {
    const [rows] = await req.db.query(`SELECT matches.*,series.name AS seriesname,match_type.slug AS matchtype,
JSON_UNQUOTE(JSON_EXTRACT(matches.team_id, '$[0]')) AS home_teamid,
JSON_UNQUOTE(JSON_EXTRACT(matches.team_id, '$[1]')) AS visitorteam_id,
t1.name AS hometeam_name,t2.name AS visitorteam_name,t1.image AS hometeam_image,t2.image AS visitorteam_image,t1.slug AS hometeam_short_name,t2.slug AS visitorteam_short_name  FROM my_matches 
LEFT JOIN matches ON matches.id=my_matches.match_id
LEFT JOIN series ON series.id=matches.series_id
LEFT JOIN match_type ON match_type.id=matches.matchtype_id
LEFT JOIN teams t1 ON JSON_UNQUOTE(JSON_EXTRACT(matches.team_id, '$[0]')) = t1.id
LEFT JOIN teams t2 ON JSON_UNQUOTE(JSON_EXTRACT(matches.team_id, '$[1]')) = t2.id
WHERE my_matches.userid=${userid} AND matches.game_id=${gameid} AND matches.start_date < NOW() AND matches.status=3`);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function send_otp(req,otp,mobile) {
  try {
    const [rows] = await req.db.query(`UPDATE users SET otp='${otp}' WHERE mobile='${mobile}'`);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function verify_otp(req,otp,mobile) {
  try {
    const [rows] = await req.db.query(`SELECT * FROM users WHERE otp='${otp}' && mobile='${mobile}'`);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function exist_user(req,mobile) {
  try {
    const [rows] = await req.db.query(`SELECT * FROM users WHERE status='1' && mobile='${mobile}'`);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function exist_cupon(req,cupon) {
  try {
    const [rows] = await req.db.query(`SELECT * FROM user_details WHERE Invitation_code='${cupon}'`);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function exist_mobile(req,mobile) {
  try {
    const [rows] = await req.db.query(`SELECT * FROM users WHERE mobile='${mobile}'`);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function get_profile(req,userid) {
  try {
    const [rows] = await req.db.query(`SELECT users.*,user_details.wallet,user_details.unutiliesed_wallet,user_details.winning_wallet,user_details.bonus_wallet,user_details.skill_score,user_details.Invitation_code FROM users LEFT JOIN user_details ON user_details.user_id=users.id WHERE users.id='${userid}'`);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function update_profile(req,userid,name,dob,age,gender,type,refered_by) {
  try {
    if(name)
    {
        console.log(`UPDATE users SET name='${name}' WHERE id='${userid}'`);
        const [rows] = await req.db.query(`UPDATE users SET name='${name}' WHERE id='${userid}'`);
    }
    if(dob)
    {
        const [rows] = await req.db.query(`UPDATE users SET dob='${dob}' WHERE id='${userid}'`);
    }
    if(age)
    {
        const [rows] = await req.db.query(`UPDATE users SET age='${age}' WHERE id='${userid}'`);
    }
    if(gender)
    {
        const [rows] = await req.db.query(`UPDATE users SET gender='${gender}' WHERE id='${userid}'`);
    }
    if(type=='1') // Through Signup Api
    {
        const [rows] = await req.db.query(`UPDATE users SET status='${type}',added_by='${refered_by}' WHERE id='${userid}'`);
        
        // const invite_code=generateRandomString(6)
        
        // sql=`INSERT INTO user_details(user_id, wallet,bonus_wallet,Invitation_code) VALUES ('${userid}',(SELECT value FROM settings WHERE name='Signup Bonus' && status='1'),(SELECT value FROM settings WHERE name='Signup Bonus' && status='1'),'${invite_code}')`;
        // console.log(sql);
        // const [rows2] = await req.db.query(sql);
        
        // const rows3 = await signup_transactions(req,userid);
        
        // const rows4 = await referal_transactions(req,userid,refered_by);
    }
  } catch (err) {
    throw new Error(err.message);
  }
}

async function signup_transactions(req,userid) {
  try {
      sql=`INSERT INTO transactions(userid, amount, type, sub_type, status) VALUES ('${userid}',(SELECT value FROM settings WHERE name='Signup Bonus' && status='1'),(SELECT id FROM transaction_type WHERE name='others'),(SELECT id FROM transaction_type WHERE name='Signup Bonus'),'1')`;
      console.log(sql)
        const [rows] = await req.db.query(sql);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function referal_transactions(req,userid,refered_by) {
  try {
      if(refered_by!='0')
      {
      sql=`INSERT INTO transactions(userid, amount, type, sub_type, status) VALUES ('${userid}',(SELECT value FROM settings WHERE name='Invitation Bonus' && status='1'),(SELECT id FROM transaction_type WHERE name='others'),(SELECT id FROM transaction_type WHERE name='Invitation Bonus'),'1')`;
      console.log("=====================================Invitation Bonus Insert====================================================")
      console.log(sql)
      console.log("=====================================Invitation Bonus Insert====================================================")
        const [rows] = await req.db.query(sql);
        
        sql4=`INSERT INTO transactions(userid, amount, type, sub_type, status) VALUES ('${refered_by}',(SELECT value FROM settings WHERE name='Referel Bonus' && status='1'),(SELECT id FROM transaction_type WHERE name='others'),(SELECT id FROM transaction_type WHERE name='Referal Bonus'),'1')`;
         
        console.log("=====================================Referal Bonus Insert====================================================")
      console.log(sql4)
      console.log("=====================================Referal Bonus Insert====================================================")
        const [rows4] = await req.db.query(sql4);
        
        sql2=`UPDATE user_details SET wallet=wallet+(SELECT value FROM settings WHERE name='Invitation Bonus' && status='1'),bonus_wallet=bonus_wallet+(SELECT value FROM settings WHERE name='Invitation Bonus' && status='1') WHERE user_id='${userid}'`;
      console.log("=====================================Invitation Bonus Update====================================================")
      console.log(sql2)
      console.log("=====================================Invitation Bonus Update====================================================")
      const [rows2] = await req.db.query(sql2);
      
      sql3=`UPDATE user_details SET wallet=wallet+(SELECT value FROM settings WHERE name='Referel Bonus' && status='1'),bonus_wallet=bonus_wallet+(SELECT value FROM settings WHERE name='Referel Bonus' && status='1') WHERE user_id='${refered_by}'`;
      console.log("=====================================Referal Bonus Update====================================================")
      console.log(sql3)
      console.log("=====================================Referal Bonus Update====================================================")
        const [rows3] = await req.db.query(sql3);
    return rows2;
      }
      else
      {
          return 1;
      }
  } catch (err) {
    throw new Error(err.message);
  }
}

async function update_profile_image(req,userid,image) {
  try {
   
        console.log(`UPDATE users SET image='${image}' WHERE id='${userid}'`);
        const [rows] = await req.db.query(`UPDATE users SET image='${image}' WHERE id='${userid}'`);
    
  } catch (err) {
    throw new Error(err.message);
  }
}

async function update_users_dac(req,userid,type,doc_number,doc_images) {
    try {
   
        const update_doc= await req.db.query(`INSERT INTO doc_verification(userid, type, document_number, doc_image) VALUES ('${userid}','${type}','${doc_number}','${doc_images}')`);
    
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

async function add_wallet(req,userid,amount) {
  try {
      sql=`UPDATE user_details SET wallet=wallet+${amount},unutiliesed_wallet=unutiliesed_wallet+${amount} WHERE user_id='${userid}'`;
      console.log(sql)
        const [rows] = await req.db.query(sql);
        
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function get_transaction_type(req) {
  try {
    const [rows] = await req.db.query(`SELECT * FROM transaction_type WHERE status='1'`);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function get_transaction(req,userid) {
  try {
    const [rows] = await req.db.query(`SELECT transactions.*,matches.name AS matchname,transaction_type.name AS transaction_type,tp.symbols,tp.name AS transaction_subtype FROM transactions LEFT JOIN matches ON matches.id=transactions.match_id LEFT JOIN transaction_type ON transaction_type.id=transactions.type LEFT JOIN transaction_type tp ON tp.id=transactions.sub_type WHERE userid='${userid}' ORDER BY CAST(transactions.updated_at AS DATETIME) DESC`);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function get_games(req) {
  try {
    const [rows] = await req.db.query(`SELECT * FROM games WHERE status='1'`);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function match_status(req) {
  try {
    const [rows] = await req.db.query(`SELECT * FROM match_status WHERE status='1'`);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function contest_list(req,matchid,gameid) {
  try {
    const [rows] = await req.db.query(`SELECT contests.*,contest_details.prize_pool,contest_details.entry_fee,contest_details.total_spot,
            CASE
                WHEN contest_details.success_type='1' THEN 'Guranteed'
                WHEN contest_details.success_type='2' THEN 'Flexible'
                ELSE ''
            END AS contest_success_type,
            JSON_UNQUOTE(JSON_EXTRACT(contest_winnings.winning_details, '$[0].prize')) AS first_prize,
            CASE
                WHEN contest_details.entry_limit=1 THEN 'Single'
                ELSE CONCAT('Upto ',contest_details.entry_limit)
            END AS entry_limit
            FROM contests LEFT JOIN contest_details ON contest_details.contest_id=contests.id LEFT JOIN contest_winnings ON contest_winnings.contest_id=contests.id WHERE game_id='${gameid}' AND (contests.match_id = '${matchid}' OR contests.match_id IS NULL) AND contests.status=1`);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function mycontest_list(req,userid,gameid,matchid) {
  try {
      query=`SELECT contests.*,contest_details.prize_pool,contest_details.entry_fee,contest_details.total_spot,
            CASE
                WHEN contest_details.success_type='1' THEN 'Guranteed'
                WHEN contest_details.success_type='2' THEN 'Flexible'
                ELSE 'None'
            END AS contest_success_type,
            JSON_UNQUOTE(JSON_EXTRACT(contest_winnings.winning_details, '$[0].prize')) AS first_prize,
            CASE
                WHEN contest_details.entry_limit=1 THEN 'Single'
                ELSE CONCAT('Upto ',contest_details.entry_limit)
            END AS entry_limit FROM my_contest 
            LEFT JOIN my_matches ON my_matches.id=my_contest.my_match_id
            LEFT JOIN matches ON my_matches.match_id=matches.id
            LEFT JOIN contests ON my_contest.contest_id=contests.id
            LEFT JOIN contest_details ON contest_details.contest_id=contests.id
            LEFT JOIN contest_winnings ON contest_winnings.contest_id=contests.id
            WHERE user_id='${userid}' AND matches.id='${matchid}' AND matches.game_id='${gameid}'`;
    const [rows] = await req.db.query(query);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function contest_winning(req,contestid) {
  try {
      query=`SELECT contests.*,contest_details.prize_pool,contest_details.entry_fee,contest_details.total_spot,
            CASE
                WHEN contest_details.success_type='1' THEN 'Guranteed'
                WHEN contest_details.success_type='2' THEN 'Flexible'
                ELSE ''
            END AS contest_success_type,contest_winnings.winning_details,
            CASE
                WHEN contest_details.entry_limit=1 THEN 'Single'
                ELSE CONCAT('Upto ',contest_details.entry_limit)
            END AS entry_limit
            FROM contests 
            LEFT JOIN contest_details ON contest_details.contest_id=contests.id 
            LEFT JOIN contest_winnings ON contest_winnings.contest_id=contests.id WHERE contest_winnings.contest_id='${contestid}' AND contests.status=1`;
    const [rows] = await req.db.query(query);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function match_details_byid(req,matchid) {
  try {
    const [rows] = await req.db.query(`SELECT matches.* FROM matches WHERE id='${matchid}'`);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function contest_filter(req) {
  try {
    const [rows] = await req.db.query(`SELECT *  FROM contest_filter WHERE Status=1`);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function get_banner(req,type) {
  try {
    const [rows] = await req.db.query(`SELECT * FROM banners WHERE status='1' AND type='${type}'`);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function withdraw_wallet(req,userid,amount) {
  try {
      sql=`UPDATE user_details SET wallet=wallet-${amount},winning_wallet=winning_wallet-${amount} WHERE user_id='${userid}'`;
      console.log(sql)
        const [rows] = await req.db.query(sql);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function get_withdraw_amount(req,userid,amount) {
  try {
      sql=`SELECT * FROM user_details WHERE winning_wallet>=${amount} AND  user_id='${userid}'`;
      console.log(sql)
        const [rows] = await req.db.query(sql);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function min_withdrawal_amount(req) {
  try {
    const [rows] = await req.db.query(`SELECT value FROM settings WHERE name='Min Withdrawal Amount' && status='1'`);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function contestFilterWiseData(req,filtertype,filter_value,gameid,matchid) {
  try {
        sql=`SELECT contests.*,contest_details.prize_pool,contest_details.entry_fee,contest_details.total_spot,
            CASE
                WHEN contest_details.success_type='1' THEN 'Guranteed'
                WHEN contest_details.success_type='2' THEN 'Flexible'
                ELSE ''
            END AS contest_success_type,
            JSON_UNQUOTE(JSON_EXTRACT(contest_winnings.winning_details, '$[0].prize')) AS first_prize,
            CASE
                WHEN contest_details.entry_limit=1 THEN 'Single'
                ELSE CONCAT('Upto ',contest_details.entry_limit)
            END AS entry_limit
            FROM contests LEFT JOIN contest_details ON contest_details.contest_id=contests.id LEFT JOIN contest_winnings ON contest_winnings.contest_id=contests.id WHERE game_id='${gameid}' AND (contests.match_id = '${matchid}' OR contests.match_id IS NULL) AND contests.status=1 AND contest_details.total_spot ${filter_value}`;
            console.log(sql)
        const [rows] = await req.db.query(sql);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function get_players_bySeries(req,seriesId) {
  try {
    const [rows] = await req.db.query(`SELECT players.*,teams.name AS teamname,teams.slug AS teamslug,teams.image AS teamimage FROM players LEFT JOIN teams ON teams.id=players.teamid WHERE series_id='${seriesId}'`);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function my_date_wiserefreals(req,userid,date) {
  try {
      var sql=`WITH RECURSIVE UserHierarchy AS (
    -- Base case: select users added by user with id 1
    SELECT *
    FROM users
    WHERE added_by = '${userid}'
    UNION ALL
    -- Recursive case: select users added by users found in the previous level
    SELECT u.*
    FROM users u
    INNER JOIN UserHierarchy uh ON u.added_by = uh.id
)
SELECT * FROM UserHierarchy`;
      console.log('=======================================Direct_subordinate=================================================')
      console.log(sql)
      console.log('=======================================Direct_subordinate=================================================')
    const [rows] = await req.db.query(sql);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function direct_subordinate(req,userid,date) {
  try {
      var sql=`SELECT JSON_ARRAYAGG( JSON_OBJECT(
          'NoOfRegister', (SELECT count(*) AS counts From users WHERE added_by='${userid}' AND DATE(created_at)='${date}'),
          'DepositeNumber', (SELECT COUNT(id) AS counts FROM transactions WHERE userid IN (SELECT id FROM users  WHERE added_by='${userid}') AND DATE(created_at)='${date}' AND type='1'),
          'DepositeAmount',(SELECT SUM(amount) AS amount FROM transactions WHERE userid IN (SELECT id FROM users  WHERE added_by='${userid}') AND DATE(created_at)='${date}'  AND type='1'),
          'FirstDepositeCount',(SELECT COUNT(*) FROM (SELECT id FROM transactions WHERE userid IN (SELECT id FROM users WHERE added_by = '${userid}') AND DATE(created_at) = '${date}' AND type = '1' GROUP BY userid ORDER BY MIN(created_at)) AS first_row_per_userid)) ) AS json_result FROM users`;
      console.log('=======================================Direct_subordinate=================================================')
      console.log(sql)
      console.log('=======================================Direct_subordinate=================================================')
    const [rows] = await req.db.query(sql);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function team_subordinate(req,userid,date) {
  try {
      var sql=`WITH RECURSIVE user_hierarchy AS (SELECT * FROM users WHERE added_by = '${userid}' UNION ALL SELECT u.* FROM users u INNER JOIN user_hierarchy uh ON u.added_by = uh.id ) SELECT JSON_ARRAYAGG(JSON_OBJECT(
        'NoOfRegister', (SELECT COUNT(*) FROM users WHERE id IN (SELECT id FROM user_hierarchy WHERE added_by != '${userid}' AND DATE(created_at) = '${date}')),
        'DepositeNumber', (SELECT COUNT(amount) FROM transactions WHERE userid IN (SELECT id FROM user_hierarchy WHERE added_by != '${userid}') AND DATE(created_at) = '${date}' AND type = '1' ),
        'DepositeAmount', (SELECT SUM(amount) FROM transactions WHERE userid IN (SELECT id FROM user_hierarchy WHERE added_by != '${userid}') AND DATE(created_at) = '${date}' AND type = '1' ),
        'FirstDepositeCount', (SELECT COUNT(*) FROM ( SELECT id FROM transactions WHERE userid IN (SELECT id FROM user_hierarchy WHERE added_by != '${userid}' ) AND DATE(created_at) = '${date}' AND type = '1' GROUP BY userid ORDER BY MIN(created_at) ) AS first_row_per_userid ) ) ) AS json_result FROM users;`;
      console.log('=======================================team_subordinate=================================================')
      console.log(sql)
      console.log('=======================================team_subordinate=================================================')
    const [rows] = await req.db.query(sql);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function my_refreals(req,userid,type) {
  try {
        if(type=='0') // All Referels
        {
              var sql=`SELECT 
                    u.id,u.username,u.name,u.created_at,
                    'Direct Subordinate' AS referral_type
                FROM users u
                WHERE u.added_by = '${userid}'`;
        }
        if(type=='1') // Today
        {
              var sql=`WITH RECURSIVE UserHierarchy AS (
                SELECT 
                    u.id,u.username,u.name,u.created_at,
                    'Direct Subordinate' AS referral_type
                FROM users u
                WHERE u.added_by = '${userid}' 
                
                UNION ALL
                
                -- Recursive case: select users added by users found in the previous level
                SELECT 
                    u.id,u.username,u.name,u.created_at,
                    'Team Subordinate' AS referral_type
                FROM users u
                INNER JOIN UserHierarchy uh ON u.added_by = uh.id
            )
            SELECT * FROM UserHierarchy WHERE DATE(created_at)=CURDATE() ;
            `;
        }
        if(type=='2')  // Yerterday
        {
              var sql=`WITH RECURSIVE UserHierarchy AS (
                SELECT 
                    u.id,u.username,u.name,u.created_at,
                    'Direct Subordinate' AS referral_type
                FROM users u
                WHERE u.added_by = '${userid}'
                
                UNION ALL
                
                -- Recursive case: select users added by users found in the previous level
                SELECT 
                    u.id,u.username,u.name,u.created_at,
                    'Team Subordinate' AS referral_type
                FROM users u
                INNER JOIN UserHierarchy uh ON u.added_by = uh.id
            )
            SELECT * FROM UserHierarchy WHERE DATE(created_at)=CURDATE()-1;
            `;
        }
        if(type=='3') // This Month
        {
            date=moment().date();
              var sql=`WITH RECURSIVE UserHierarchy AS (
                SELECT 
                    u.id,u.username,u.name,u.created_at,
                    'Direct Subordinate' AS referral_type
                FROM users u
                WHERE u.added_by = '${userid}'
                
                UNION ALL
                
                -- Recursive case: select users added by users found in the previous level
                SELECT 
                    u.id,u.username,u.name,u.created_at,
                    'Team Subordinate' AS referral_type
                FROM users u
                INNER JOIN UserHierarchy uh ON u.added_by = uh.id
            )
            SELECT * FROM UserHierarchy WHERE DATE(created_at)>CURDATE()-${date};
            `;
        }
      console.log('=======================================Direct_subordinate=================================================')
      console.log(sql)
      console.log('=======================================Direct_subordinate=================================================')
    const [rows] = await req.db.query(sql);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function my_tear_wise_subordinatedata(req,userid,date,tear) {
  try {
        if(!date)
        {
            var sql=`WITH RECURSIVE UserHierarchy AS (
                SELECT *, 1 as level
                FROM users
                WHERE added_by = '${userid}'
                UNION ALL
                -- Recursive case: select users added by users found in the previous level
                SELECT u.*, uh.level + 1
                FROM users u
                INNER JOIN UserHierarchy uh ON u.added_by = uh.id
                WHERE uh.level < ${tear}
            )
            SELECT UserHierarchy.*, COALESCE(SUM(t.amount), 0) AS deposit_amount,COALESCE(SUM(b.amount), 0) AS bet_amount,COALESCE(SUM(com.amount), 0) AS commission_amount FROM UserHierarchy LEFT JOIN transactions t ON t.userid = UserHierarchy.id AND t.type = (SELECT id FROM transaction_type WHERE name='Deposit') LEFT JOIN transactions b ON b.userid = UserHierarchy.id AND b.type = (SELECT id FROM transaction_type WHERE name='Contest') LEFT JOIN transactions com ON com.userid = UserHierarchy.id AND com.type = (SELECT id FROM transaction_type WHERE name='Commisson') GROUP BY UserHierarchy.id, UserHierarchy.level;`;
        }
        else{
            var sql=`WITH RECURSIVE UserHierarchy AS (
                SELECT *, 1 as level
                FROM users
                WHERE added_by = '${userid}' AND DATE(created_at)='${date}'
                UNION ALL
                -- Recursive case: select users added by users found in the previous level
                SELECT u.*, uh.level + 1
                FROM users u
                INNER JOIN UserHierarchy uh ON u.added_by = uh.id
                WHERE uh.level < ${tear}
            )
            SELECT UserHierarchy.*, COALESCE(SUM(t.amount), 0) AS deposit_amount,COALESCE(SUM(b.amount), 0) AS bet_amount,COALESCE(SUM(com.amount), 0) AS commission_amount FROM UserHierarchy LEFT JOIN transactions t ON t.userid = UserHierarchy.id AND t.type = (SELECT id FROM transaction_type WHERE name='Deposit') LEFT JOIN transactions b ON b.userid = UserHierarchy.id AND b.type = (SELECT id FROM transaction_type WHERE name='Contest') LEFT JOIN transactions com ON com.userid = UserHierarchy.id AND com.type = (SELECT id FROM transaction_type WHERE name='Commisson') GROUP BY UserHierarchy.id, UserHierarchy.level;`;
        }
      console.log('=======================================team_subordinate=================================================')
      console.log(sql)
      console.log('=======================================team_subordinate=================================================')
    const [rows] = await req.db.query(sql);
    return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function update_payin_status(req,userid,transactionid,utr,status) {
  try {
   
        sql=`UPDATE transactions SET utr='${utr}',status='${status}' WHERE userid='${userid}' AND transaction_id='${transactionid}'`;
        const [rows] = await req.db.query(sql);
        
  } catch (err) {
    throw new Error(err.message);
  }
}

async function get_settings(req,name) {
  try {
   
        sql=`SELECT id,name,value FROM settings WHERE name='${name}'`;
        console.log(sql)
        const [rows] = await req.db.query(sql);
        return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function how_to_play(req,gameid) {
  try {
   
        sql=`SELECT * FROM how_to_play WHERE gameid='${gameid}'`;
        console.log(sql)
        const [rows] = await req.db.query(sql);
        return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function get_notifications(req,type,userid,count_type) {
  try {
        if(type=='1')
        {
            sql=`SELECT n.*,CASE
        WHEN nv.userid IS NOT NULL THEN '1'
        ELSE '0'
        END AS is_viewed FROM  notifications n LEFT JOIN  (SELECT DISTINCT notification_id, userid FROM notification_viewed WHERE userid = '${userid}') nv ON  nv.notification_id = n.id WHERE  n.userid IN (${userid}, 0) GROUP BY  n.id`;
        }
        if(type=='2')
        {
            sql=`SELECT n.*,CASE
        WHEN nv.userid IS NOT NULL THEN '1'
        ELSE '0'
        END AS is_viewed FROM  notifications n LEFT JOIN  (SELECT DISTINCT notification_id, userid FROM notification_viewed WHERE userid = '${userid}') nv ON  nv.notification_id = n.id WHERE  n.userid IN (${userid}, 0) AND n.type='${type}' GROUP BY  n.id `;
        }
        if(count_type=='3')
        {
            sql=`SELECT * FROM (SELECT n.*,CASE
        WHEN nv.userid IS NOT NULL THEN '1'
        ELSE '0'
        END AS is_viewed FROM  notifications n LEFT JOIN  (SELECT DISTINCT notification_id, userid FROM notification_viewed WHERE userid = '${userid}') nv ON  nv.notification_id = n.id WHERE  n.userid IN (${userid}, 0) AND n.type='${type}' GROUP BY  n.id ) AS subquery WHERE is_viewed = '0'`;
        }
        console.log('====================================================')
        console.log(sql)
        console.log('====================================================')
        
        const [rows] = await req.db.query(sql);
        return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

async function save_viewed_notifications(req,notification_id,userid){
  try {
   
        sql=`INSERT INTO notification_viewed(notification_id, userid) VALUES ('${notification_id}','${userid}')`;
        console.log(sql)
        const [rows] = await req.db.query(sql);
        return rows;
  } catch (err) {
    throw new Error(err.message);
  }
}

module.exports = {
  my_upcoming_matches,send_otp,verify_otp,exist_user,exist_mobile,get_profile,update_profile,update_profile_image,update_users_dac,min_amount,add_wallet,get_transaction_type,get_transaction,get_games,my_live_matches,my_complete_matches,match_status,contest_list,mycontest_list,upcoming_matches,contest_winning,match_details_byid,contest_filter,get_banner,withdraw_wallet,get_withdraw_amount,min_withdrawal_amount,contestFilterWiseData,get_players_bySeries,my_date_wiserefreals,direct_subordinate,team_subordinate,my_refreals,my_tear_wise_subordinatedata,exist_cupon,generateRandomString,signup_transactions,referal_transactions,update_payin_status,get_settings,how_to_play,get_notifications,save_viewed_notifications
};