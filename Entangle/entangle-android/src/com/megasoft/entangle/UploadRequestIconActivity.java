package com.megasoft.entangle;

import android.app.Activity;
import android.app.FragmentManager;
import android.app.FragmentTransaction;
import android.os.Bundle;

public class UploadRequestIconActivity extends Activity{
	private String sessionId;
	private int requestId;
	
	public int getRequestId() {
		return requestId;
	}
	
	public void setRequestId(int requestId) {
		this.requestId = requestId;
	}
	
	public String getSessionId() {
		return sessionId;
	}
	
	public void setSessionId(String sessionId) {
		this.sessionId = sessionId;
		System.out.println("hello");  
	}
	
	@Override	
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_upload_request_icon);
		
		setSessionId(getIntent().getStringExtra("sessionId"));
		setRequestId(getIntent().getIntExtra("requestId", 1));
		
		FragmentManager manager = getFragmentManager();
		FragmentTransaction transaction = manager.beginTransaction();
		transaction.add(R.id.iconUploadLayoutPlaceHolder, new PhotoUploaderFragment()).commit();
	}
}
