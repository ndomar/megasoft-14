package com.megasoft.entangle;

import android.app.Activity;
import android.app.FragmentManager;
import android.app.FragmentTransaction;
import android.os.Bundle;

import com.megasoft.config.Config;
import com.megasoft.entangle.megafragments.*;

public class UploadRequestIconActivity extends Activity{
	private int requestId;
	
	public int getRequestId() {
		return requestId;
	}
	
	public void setRequestId(int requestId) {
		this.requestId = requestId;
	}
	
	@Override	
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_upload_request_icon);
		
		setRequestId(getIntent().getIntExtra(Config.REQUEST_ID, 1));
		
		Bundle bundle = new Bundle();
		bundle.putInt(Config.REQUEST_ID, getRequestId());
		
		FragmentManager manager = getFragmentManager();
		FragmentTransaction transaction = manager.beginTransaction();

		PhotoUploaderFragment fragment = new PhotoUploaderFragment();
		fragment.setArguments(bundle);
		
		transaction.add(R.id.iconUploadLayoutPlaceHolder, fragment).commit();
	}
}
