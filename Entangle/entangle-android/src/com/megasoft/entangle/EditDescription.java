package com.example.editinfo;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.EditText;

public class EditDescription extends Activity {
	
	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		getIntent();
		setContentView(R.layout.edit_description);

	}

	public void SaveNewDescription(View view) {

		EditText newDescription = (EditText) findViewById(R.id.NewDescription);
		String text = newDescription.getText().toString();
		Log.i("tag", text);
		Intent editDes = new Intent(this, MyInfo.class);
		editDes.putExtra("Desc", text);
		startActivity(editDes);
		// toBeUpdatedDescription.setText(newDescription.getText().toString());
	}
}
