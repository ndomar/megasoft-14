package com.example.editinfo;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;

;

public class EditMyPassword extends Activity {
	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		getIntent();
		setContentView(R.layout.change_password);

	}

	public void SaveNewPassword(View view) {
		Intent editDes = new Intent(this, MyInfo.class);
		startActivity(editDes);
	}
}
