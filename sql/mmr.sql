create table mmr_ff_user(id int auto_increment primary key, 
    mmr_user_id int not null, 
    friend_feed_user_name varchar(255) not null, 
    friend_feed_auth_key varchar(255) not null, 
    last_workout_id int, 
    last_update_date timestamp,
    is_active boolean default true);
    
    
rename table mmr_ff_user to mmr_notify_user;
alter table mmr_notify_user change friend_feed_user_name app_user_name varchar(255) not null;
alter table mmr_notify_user change friend_feed_auth_key  app_auth_key  varchar(255) not null;
alter table mmr_notify_user add notify_type varchar(50);
update mmr_notify_user set notify_type ='friendfeed';
alter table mmr_notify_user modify notify_type varchar(50) not null;