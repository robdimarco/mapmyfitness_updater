create table mmr_ff_user(id int auto_increment primary key, 
    mmr_user_id int not null, 
    friend_feed_user_name varchar(255) not null, 
    friend_feed_auth_key varchar(255) not null, 
    last_workout_id int, 
    last_update_date timestamp,
    is_active boolean default true);