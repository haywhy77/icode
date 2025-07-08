<?php die();
[routes]

//  candidate login
GET|POST /=Home->candidate_login
GET|POST /signup=Home->candidate_signup
GET|POST /reset=Home->candidate_reset
GET|POST /change-password=Home->candidate_change
POST /candidate/jobs/get-jobs=Candidates->getJobs
GET /candidate/jobs/@id=Candidates->getJobsDetail
GET /candidate/upload-fix=Candidates->fixAttachment

GET|POST /application/complete-profile/@id=Home->completeProfile


// Employers login
GET|POST /client=Home->client_login
GET|POST /client/signup=Home->client_signup
GET|POST /client/password-forget=Home->client_reset

//  admin login
GET|POST /admin=Home->admin_login
GET|POST /admin/signup=Home->admin_signup
GET|POST /invitation/@id=Home->admin_invitation
GET|POST /admin/password-forget=Home->admin_reset


//Change password
GET|POST /admin/change-password=Home->admin_change
GET|POST /client/change-password=Home->client_change