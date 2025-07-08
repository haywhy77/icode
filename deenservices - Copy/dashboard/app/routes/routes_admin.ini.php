[routes]

GET /=Dashboard->index
GET /logout = Home->logout
GET /download/@extention=Export->index
GET /candidate/validate=Home->validate

GET|POST /application/complete-profile/@id=Home->completeProfile



GET|POST /account/@id=Dashboard->candidate
GET|POST /client/account/@id=Dashboard->client
GET|POST /admin/account/@id=Dashboard->admin



GET /overview=Candidates->index
GET /candidates=Candidates->lists
GET /candidates/@id=Candidates->details
GET /my-applications=Candidates->applications
POST /candidate/@id/update=Candidates->update
GET /job-listing=Candidates->jobsListing
POST /candidate/jobs/get-jobs=Candidates->getJobs
GET /candidate/jobs/@id=Candidates->getJobsDetail
GET|POST /candidate/apply/@id=Candidates->apply
GET|POST /candidate/upload-documents=Candidates->attach
GET /candidate/upload-fix=Candidates->fixAttachment
GET /candidate/upload-documents/@delete/@name/@ext=Candidates->remove


GET /jobs=Jobs->index
GET /jobs/new=Jobs->newJob
GET /jobs/@id=Jobs->getApplicants
GET /jobs/edit/@id=Jobs->editApplicants
POST /jobs/save=Jobs->save

GET|POST /jobs/locations=Jobs->locations
GET|POST /jobs/fields=Jobs->fields
GET|POST /jobs/skills=Jobs->skills
POST /jobs/misc-trash=Jobs->misc

GET /applications=Applications->index
GET /applications/new=Applications->openApplication
GET /application/@id=Applications->getApplicants
POST /applications/@id/update=Applications->updateApplications
POST /applications/ajax=Applications->getApplicationDetails



GET /employers=Companies->index
GET|POST /employers/new=Companies->create
GET /employers/@id=Companies->details
GET /employers/staff=Companies->details
POST /employers/@id/post-staff=Companies->postStaff
POST /employers/staff/remove=Companies->removeStaff
POST /employers/action/update=Companies->performActions

GET /staff=Staff->index
GET /staff/@id=Staff->detail
POST /staff/action/update=Staff->performActions
POST /staff/remove=Staff->removeStaff
GET /staff/profile-card/@action/@id=Staff->profile
POST /staff/update/profile-card=Staff->updateProfile
GET|POST /staff/@id/upload=Staff->attach
GET /staff/@id/upload/@delete/@name/@ext=Staff->remove
GET /staff/account/update/@action/@id=Staff->status

GET /staff/@id/download=Staff->downloadOther


GET /users=Users->index
GET /users/@id=Users->getUser
GET|POST /users/new=Users->create
POST /users/update=Users->update


GET /settings=Settings->index



GET /applicant/hunts=Messaging->index
GET|POST /applicant/scout=Messaging->scout
GET|POST /messaging/compose=Messaging->compose
POST /messaging/update/hunts=Messaging->performs

[maps]
