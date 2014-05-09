package com.megasoft.entangle;

import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

public class FrozenRequestFragment extends StreamRequestFragment {
	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
			Bundle savedInstanceState) {
		view = inflater.inflate(R.layout.fragment_frozen_request, container,
				false);
		setAttributes();
		return view;
	}

}
