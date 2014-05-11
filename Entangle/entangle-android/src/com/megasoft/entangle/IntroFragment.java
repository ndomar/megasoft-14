package com.megasoft.entangle;

import android.content.Intent;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;

public class IntroFragment extends Fragment {

	private Button redirectToCreateTangle;

	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
			Bundle savedInstanceState) {
		
		View view = inflater.inflate(R.layout.activity_intro, container, false);
		redirectToCreateTangle = (Button) view.findViewById(R.id.here);
		redirectToCreateTangle.setOnClickListener(new Button.OnClickListener() {

			@Override
			public void onClick(View v) {
				Intent intent = new Intent(getActivity(),
						CreateTangleActivity.class);
				startActivity(intent);

			}
		});
		return view;
		
	}
}