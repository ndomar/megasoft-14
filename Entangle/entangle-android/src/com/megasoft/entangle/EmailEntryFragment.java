package com.megasoft.entangle;

import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.EditText;

public class EmailEntryFragment extends Fragment {
	private EditText editText;
	private Button removeButton;
	public View view;
	private InviteUserActivity activity;
	

	TextWatcher watcher = new TextWatcher() {
		
		@Override
		public void onTextChanged(CharSequence s, int start, int before, int count) {}
		
		@Override
		public void beforeTextChanged(CharSequence s, int start, int count,
				int after) {}
		
		@Override
		public void afterTextChanged(Editable s) {
			editText.setError(null);
			if(s.length() >= 1){
				editText.removeTextChangedListener(this);
				activity.addEmailField();
			}
		}
	};

	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
            Bundle savedInstanceState) {
		this.view = inflater.inflate(R.layout.fragment_invite_user_email_field, container,false);
		this.editText = (EditText) view.findViewById(R.id.invite_user_edit_text);
		this.removeButton = (Button) view.findViewById(R.id.invite_user_remove_edit_text);
		setlisteners();
		setTextChangeListener();
		return view;
	}
	
	public void setTextChangeListener() {
		editText.addTextChangedListener(watcher);
	}

	private void setlisteners() {
		
		removeButton.setOnClickListener(new View.OnClickListener() {
			@Override
			public void onClick(View v) {
				activity.removeEmailField(EmailEntryFragment.this);
			}
		});

	}

	public String getEmail(){
		return this.editText.getText().toString();
	}
	
	public EditText getEditText(){
		return this.editText;
	}
	
	public void setActivity(InviteUserActivity activity){
		this.activity = activity;
	}
}
